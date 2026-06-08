using System.Text.Json;
using Ikhana.Application.Common.Interfaces;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Identity.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.ChangeTracking;

namespace Ikhana.Infrastructure.Persistence;

public class AppDbContext : IdentityDbContext<ApplicationUser, ApplicationRole, long>, IAppDbContext
{
    private readonly IHttpContextAccessor? _httpContextAccessor;

    public DbSet<AuditLog> AuditLogs => Set<AuditLog>();
    public DbSet<TaxStatus> TaxStatuses => Set<TaxStatus>();
    public DbSet<Agreement> Agreements => Set<Agreement>();
    public DbSet<Category> Categories => Set<Category>();
    public DbSet<Broker> Brokers => Set<Broker>();
    public DbSet<Provider> Providers => Set<Provider>();

    public AppDbContext(DbContextOptions<AppDbContext> options)
        : base(options)
    {
    }

    public AppDbContext(DbContextOptions<AppDbContext> options, IHttpContextAccessor httpContextAccessor)
        : base(options)
    {
        _httpContextAccessor = httpContextAccessor;
    }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);
        modelBuilder.ApplyConfigurationsFromAssembly(typeof(AppDbContext).Assembly);
    }

    public override async Task<int> SaveChangesAsync(CancellationToken cancellationToken = default)
    {
        SetTimestamps();
        HandleSoftDeletes();
        var auditEntries = CaptureAuditEntries();

        var result = await base.SaveChangesAsync(cancellationToken);

        await PersistAuditLogs(auditEntries, cancellationToken);

        return result;
    }

    private void SetTimestamps()
    {
        foreach (var entry in ChangeTracker.Entries<BaseEntity>())
        {
            switch (entry.State)
            {
                case EntityState.Added:
                    entry.Entity.CreatedAt = DateTime.UtcNow;
                    entry.Entity.UpdatedAt = DateTime.UtcNow;
                    break;
                case EntityState.Modified:
                    entry.Entity.UpdatedAt = DateTime.UtcNow;
                    break;
            }
        }
    }

    private void HandleSoftDeletes()
    {
        foreach (var entry in ChangeTracker.Entries<ISoftDelete>())
        {
            if (entry.State == EntityState.Deleted)
            {
                entry.State = EntityState.Modified;
                entry.Entity.DeletedAt = DateTime.UtcNow;
            }
        }
    }

    private List<AuditEntry> CaptureAuditEntries()
    {
        var entries = new List<AuditEntry>();
        var userId = GetCurrentUserId();

        foreach (var entry in ChangeTracker.Entries())
        {
            if (entry.Entity is not IAuditable) continue;
            if (entry.State == EntityState.Detached || entry.State == EntityState.Unchanged) continue;

            var auditEntry = new AuditEntry
            {
                TableName = entry.Metadata.GetTableName() ?? entry.Entity.GetType().Name,
                UserId = userId
            };

            foreach (var property in entry.Properties)
            {
                if (property.IsTemporary)
                {
                    auditEntry.TemporaryProperties.Add(property);
                    continue;
                }

                var propertyName = property.Metadata.Name;
                if (propertyName == "CreatedAt" || propertyName == "UpdatedAt" || propertyName == "DeletedAt")
                    continue;

                switch (entry.State)
                {
                    case EntityState.Added:
                        auditEntry.Action = "Created";
                        auditEntry.NewValues[propertyName] = property.CurrentValue;
                        break;
                    case EntityState.Deleted:
                        auditEntry.Action = "Deleted";
                        auditEntry.OldValues[propertyName] = property.OriginalValue;
                        break;
                    case EntityState.Modified:
                        auditEntry.Action = "Updated";
                        if (property.IsModified)
                        {
                            auditEntry.OldValues[propertyName] = property.OriginalValue;
                            auditEntry.NewValues[propertyName] = property.CurrentValue;
                        }
                        break;
                }
            }

            entries.Add(auditEntry);
        }

        return entries;
    }

    private async Task PersistAuditLogs(List<AuditEntry> auditEntries, CancellationToken cancellationToken)
    {
        if (auditEntries.Count == 0) return;

        foreach (var auditEntry in auditEntries)
        {
            foreach (var prop in auditEntry.TemporaryProperties)
            {
                var propertyName = prop.Metadata.Name;
                auditEntry.NewValues[propertyName] = prop.CurrentValue;
            }

            foreach (var entry in ChangeTracker.Entries())
            {
                if (entry.Entity is not IAuditable) continue;
                var keyValues = GetKeyValues(entry);
                auditEntry.KeyValues = keyValues;
            }
        }

        foreach (var auditEntry in auditEntries)
        {
            AuditLogs.Add(new AuditLog
            {
                UserId = auditEntry.UserId,
                Action = auditEntry.Action,
                TableName = auditEntry.TableName,
                KeyValues = auditEntry.KeyValues,
                OldValues = auditEntry.OldValues.Count > 0 ? JsonSerializer.Serialize(auditEntry.OldValues) : null,
                NewValues = auditEntry.NewValues.Count > 0 ? JsonSerializer.Serialize(auditEntry.NewValues) : null,
                CreatedAt = DateTime.UtcNow
            });
        }

        await base.SaveChangesAsync(cancellationToken);
    }

    private static string GetKeyValues(EntityEntry entry)
    {
        var key = entry.Metadata.FindPrimaryKey();
        if (key == null) return "{}";
        var keyValues = key.Properties.ToDictionary(p => p.Name, p => entry.Property(p.Name).CurrentValue);
        return JsonSerializer.Serialize(keyValues);
    }

    private long GetCurrentUserId()
    {
        var userIdClaim = _httpContextAccessor?.HttpContext?.User?.FindFirst(
            System.Security.Claims.ClaimTypes.NameIdentifier)?.Value;

        return long.TryParse(userIdClaim, out var userId) ? userId : 0;
    }

    private sealed class AuditEntry
    {
        public string Action { get; set; } = string.Empty;
        public string TableName { get; set; } = string.Empty;
        public long UserId { get; set; }
        public string? KeyValues { get; set; }
        public Dictionary<string, object?> OldValues { get; } = new();
        public Dictionary<string, object?> NewValues { get; } = new();
        public List<PropertyEntry> TemporaryProperties { get; } = new();
    }
}

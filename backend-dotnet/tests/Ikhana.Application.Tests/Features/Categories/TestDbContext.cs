using Ikhana.Application.Common.Interfaces;
using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Tests.Features.Categories;

public class TestDbContext : DbContext, IAppDbContext
{
    public DbSet<Category> Categories => Set<Category>();
    public DbSet<TaxStatus> TaxStatuses => Set<TaxStatus>();
    public DbSet<Agreement> Agreements => Set<Agreement>();
    public DbSet<Broker> Brokers => Set<Broker>();

    public TestDbContext(DbContextOptions<TestDbContext> options) : base(options) { }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        modelBuilder.Entity<Category>(entity =>
        {
            entity.HasKey(c => c.Id);
            entity.Property(c => c.Name).HasMaxLength(128).IsRequired();
            entity.Property(c => c.Description).HasMaxLength(512);
            entity.Property(c => c.CreatedAt);
            entity.Property(c => c.UpdatedAt);
            entity.Property(c => c.DeletedAt);
        });
    }
}

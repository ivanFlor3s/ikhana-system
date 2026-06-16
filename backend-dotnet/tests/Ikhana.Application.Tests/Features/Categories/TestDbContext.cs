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
    public DbSet<RawMaterialType> RawMaterialTypes => Set<RawMaterialType>();
    public DbSet<RawMaterialCharacteristic> RawMaterialCharacteristics => Set<RawMaterialCharacteristic>();
    public DbSet<IramOhmMaxResistance> IramOhmMaxResistances => Set<IramOhmMaxResistance>();
    public DbSet<Provider> Providers => Set<Provider>();
    public DbSet<ProviderInventory> ProviderInventories => Set<ProviderInventory>();
    public DbSet<ProviderCoilMovement> ProviderCoilMovements => Set<ProviderCoilMovement>();

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
            entity.Ignore(c => c.Providers);
        });

        modelBuilder.Entity<Provider>(entity =>
        {
            entity.HasKey(p => p.Id);
            entity.Ignore(p => p.TaxStatus);
            entity.Ignore(p => p.Agreement);
            entity.Ignore(p => p.Categories);
            entity.Ignore(p => p.Brokers);
            entity.Ignore(p => p.Inventory);
            entity.Ignore(p => p.RawMaterialEntries);
            entity.Ignore(p => p.CoilMovements);
        });

        modelBuilder.Entity<ProviderInventory>(entity =>
        {
            entity.HasKey(i => i.Id);
        });

        modelBuilder.Entity<ProviderCoilMovement>(entity =>
        {
            entity.HasKey(m => m.Id);
            entity.Ignore(m => m.Entry);
        });
    }
}

using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Ikhana.Infrastructure.Persistence.Configurations;

public class ProviderInventoryConfig : IEntityTypeConfiguration<ProviderInventory>
{
    public void Configure(EntityTypeBuilder<ProviderInventory> builder)
    {
        builder.HasIndex(i => i.ProviderId).IsUnique();

        builder.HasOne(i => i.Provider)
               .WithOne(p => p.Inventory)
               .HasForeignKey<ProviderInventory>(i => i.ProviderId)
               .OnDelete(DeleteBehavior.Cascade);
    }
}

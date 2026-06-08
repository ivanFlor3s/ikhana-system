using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Ikhana.Infrastructure.Persistence.Configurations;

public class ProviderCoilMovementConfig : IEntityTypeConfiguration<ProviderCoilMovement>
{
    public void Configure(EntityTypeBuilder<ProviderCoilMovement> builder)
    {
        builder.Property(m => m.Type).HasMaxLength(20).HasConversion<string>();

        builder.HasOne(m => m.Provider)
               .WithMany(p => p.CoilMovements)
               .HasForeignKey(m => m.ProviderId)
               .OnDelete(DeleteBehavior.Cascade);

        builder.HasOne(m => m.Entry)
               .WithMany(e => e.CoilMovements)
               .HasForeignKey(m => m.RawMaterialEntryId)
               .OnDelete(DeleteBehavior.SetNull);
    }
}

using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Ikhana.Infrastructure.Persistence.Configurations;

public class RawMaterialEntryConfig : IEntityTypeConfiguration<RawMaterialEntry>
{
    public void Configure(EntityTypeBuilder<RawMaterialEntry> builder)
    {
        builder.Property(e => e.Remito).HasMaxLength(50);
        builder.Property(e => e.QuantityKg).HasColumnType("decimal(12,3)");
        builder.Property(e => e.Observations).HasColumnType("text");

        builder.HasIndex(e => new { e.RawMaterialTypeId, e.EntryDate });

        builder.HasOne(e => e.RawMaterialType)
               .WithMany()
               .HasForeignKey(e => e.RawMaterialTypeId)
               .OnDelete(DeleteBehavior.Restrict);

        builder.HasOne(e => e.Provider)
               .WithMany(p => p.RawMaterialEntries)
               .HasForeignKey(e => e.ProviderId)
               .OnDelete(DeleteBehavior.Restrict);

        builder.HasOne(e => e.RawMaterialCharacteristic)
               .WithMany()
               .HasForeignKey(e => e.RawMaterialCharacteristicId)
               .OnDelete(DeleteBehavior.Restrict);

        builder.Ignore(e => e.ReturnedCoilsCount);
    }
}

using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Ikhana.Infrastructure.Persistence.Configurations;

public class RawMaterialCharacteristicConfig : IEntityTypeConfiguration<RawMaterialCharacteristic>
{
    public void Configure(EntityTypeBuilder<RawMaterialCharacteristic> builder)
    {
        builder.Property(c => c.Name).HasMaxLength(50).IsRequired();
        builder.Property(c => c.DecimalValue).HasColumnType("decimal(10,3)");
        builder.Property(c => c.TextValue).HasMaxLength(100);
        builder.Property(c => c.Unit).HasMaxLength(20);

        builder.HasIndex(c => new { c.RawMaterialTypeId, c.Name, c.DecimalValue })
               .IsUnique(true);

        builder.HasIndex(c => new { c.RawMaterialTypeId, c.Name, c.TextValue })
               .IsUnique(true);

        builder.HasOne(c => c.RawMaterialType)
               .WithMany(t => t.Characteristics)
               .HasForeignKey(c => c.RawMaterialTypeId)
               .OnDelete(DeleteBehavior.Cascade);

        builder.Ignore(c => c.Description);
    }
}

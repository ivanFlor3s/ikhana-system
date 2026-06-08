using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Ikhana.Infrastructure.Persistence.Configurations;

public class IramOhmMaxResistanceConfig : IEntityTypeConfiguration<IramOhmMaxResistance>
{
    public void Configure(EntityTypeBuilder<IramOhmMaxResistance> builder)
    {
        builder.Property(r => r.MaxResistanceOhmKm).HasColumnType("decimal(10,2)").IsRequired();

        builder.HasIndex(r => r.RawMaterialCharacteristicId).IsUnique();

        builder.HasOne(r => r.RawMaterialCharacteristic)
               .WithOne(c => c.IramOhmMaxResistance)
               .HasForeignKey<IramOhmMaxResistance>(r => r.RawMaterialCharacteristicId)
               .OnDelete(DeleteBehavior.Cascade);
    }
}

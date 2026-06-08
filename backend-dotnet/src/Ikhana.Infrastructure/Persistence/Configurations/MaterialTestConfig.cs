using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Ikhana.Infrastructure.Persistence.Configurations;

public class MaterialTestConfig : IEntityTypeConfiguration<MaterialTest>
{
    public void Configure(EntityTypeBuilder<MaterialTest> builder)
    {
        builder.Property(t => t.ResistanceOhmKm).HasColumnType("decimal(10,2)");
        builder.Property(t => t.ElongationPct).HasColumnType("decimal(5,2)");
        builder.Property(t => t.Result).HasMaxLength(10).HasConversion<string>();
        builder.Property(t => t.ConductedBy).HasMaxLength(120);
        builder.Property(t => t.ApprovedBy).HasMaxLength(120);

        builder.HasIndex(t => t.RawMaterialEntryId).IsUnique();

        builder.HasOne(t => t.Entry)
               .WithOne(e => e.Test)
               .HasForeignKey<MaterialTest>(t => t.RawMaterialEntryId)
               .OnDelete(DeleteBehavior.Cascade);
    }
}

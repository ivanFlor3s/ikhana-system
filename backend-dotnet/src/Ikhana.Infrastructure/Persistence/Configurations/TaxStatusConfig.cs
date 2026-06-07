using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Ikhana.Infrastructure.Persistence.Configurations;

public class TaxStatusConfig : IEntityTypeConfiguration<TaxStatus>
{
    public void Configure(EntityTypeBuilder<TaxStatus> builder)
    {
        builder.Property(t => t.Code).HasMaxLength(10).IsRequired();
        builder.Property(t => t.Name).HasMaxLength(128).IsRequired();
        builder.Property(t => t.Description).HasMaxLength(512);

        builder.HasIndex(t => t.Code).IsUnique();
    }
}

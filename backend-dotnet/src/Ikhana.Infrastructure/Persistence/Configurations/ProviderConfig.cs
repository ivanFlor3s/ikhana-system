using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Ikhana.Infrastructure.Persistence.Configurations;

public class ProviderConfig : IEntityTypeConfiguration<Provider>
{
    public void Configure(EntityTypeBuilder<Provider> builder)
    {
        builder.Property(p => p.FantasyName).HasMaxLength(255);
        builder.Property(p => p.BusinessName).HasMaxLength(255);
        builder.Property(p => p.Cuit).HasMaxLength(16);
        builder.Property(p => p.Iibb).HasMaxLength(255);
        builder.Property(p => p.Address).HasColumnType("text");
        builder.Property(p => p.Website).HasMaxLength(512);
        builder.Property(p => p.ContactName).HasMaxLength(255);
        builder.Property(p => p.Observations).HasColumnType("text");

        builder.HasIndex(p => p.Cuit).IsUnique();

        builder.HasOne(p => p.TaxStatus)
               .WithMany(t => t.Providers)
               .HasForeignKey(p => p.TaxStatusId)
               .OnDelete(DeleteBehavior.SetNull);

        builder.HasOne(p => p.Agreement)
               .WithMany(a => a.Providers)
               .HasForeignKey(p => p.AgreementId)
               .OnDelete(DeleteBehavior.SetNull);

        builder.HasMany(p => p.Brokers)
               .WithOne(b => b.Provider)
               .HasForeignKey(b => b.ProviderId)
               .OnDelete(DeleteBehavior.SetNull);

        builder.HasMany(p => p.Categories)
               .WithMany(c => c.Providers)
               .UsingEntity(j => j.ToTable("ProviderCategories"));
    }
}

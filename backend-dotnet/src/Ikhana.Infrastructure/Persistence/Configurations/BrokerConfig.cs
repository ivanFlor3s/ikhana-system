using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata.Builders;

namespace Ikhana.Infrastructure.Persistence.Configurations;

public class BrokerConfig : IEntityTypeConfiguration<Broker>
{
    public void Configure(EntityTypeBuilder<Broker> builder)
    {
        builder.Property(b => b.FirstName).HasMaxLength(128).IsRequired();
        builder.Property(b => b.LastName).HasMaxLength(128).IsRequired();
        builder.Property(b => b.Email).HasMaxLength(256).IsRequired();
        builder.Property(b => b.Phone).HasMaxLength(32);

        builder.HasIndex(b => b.Email).IsUnique();

        builder.Ignore(b => b.FullName);
    }
}

using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Infrastructure.Persistence.Configurations;

public class AuditLogConfig : IEntityTypeConfiguration<AuditLog>
{
    public void Configure(Microsoft.EntityFrameworkCore.Metadata.Builders.EntityTypeBuilder<AuditLog> builder)
    {
        builder.HasKey(a => a.Id);

        builder.Property(a => a.Action).HasMaxLength(20).IsRequired();
        builder.Property(a => a.TableName).HasMaxLength(128).IsRequired();

        builder.HasOne(a => a.User)
               .WithMany()
               .HasForeignKey(a => a.UserId)
               .OnDelete(DeleteBehavior.Cascade);

        builder.HasIndex(a => a.CreatedAt);
        builder.HasIndex(a => a.Action);
        builder.HasIndex(a => a.TableName);
        builder.HasIndex(a => a.UserId);
    }
}

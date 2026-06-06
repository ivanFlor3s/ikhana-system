using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Infrastructure.Persistence.Configurations;

public class ApplicationRoleConfig : IEntityTypeConfiguration<ApplicationRole>
{
    public void Configure(Microsoft.EntityFrameworkCore.Metadata.Builders.EntityTypeBuilder<ApplicationRole> builder)
    {
        builder.Property(r => r.Description).HasMaxLength(512);
    }
}

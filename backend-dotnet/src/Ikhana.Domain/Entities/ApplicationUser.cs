using Microsoft.AspNetCore.Identity;

namespace Ikhana.Domain.Entities;

public class ApplicationUser : IdentityUser<long>
{
    public string Name { get; set; } = string.Empty;
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
    public DateTime UpdatedAt { get; set; } = DateTime.UtcNow;
}

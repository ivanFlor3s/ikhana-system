using Microsoft.AspNetCore.Identity;

namespace Ikhana.Domain.Entities;

public class ApplicationRole : IdentityRole<long>
{
    public string? Description { get; set; }
}

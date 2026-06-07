using System.ComponentModel.DataAnnotations.Schema;

namespace Ikhana.Domain.Entities;

public class Broker : BaseEntity, ISoftDelete, IAuditable
{
    public string FirstName { get; set; } = string.Empty;
    public string LastName { get; set; } = string.Empty;
    public string Email { get; set; } = string.Empty;
    public string? Phone { get; set; }
    public DateTime? DeletedAt { get; set; }

    [NotMapped]
    public string FullName => $"{FirstName} {LastName}".Trim();

}

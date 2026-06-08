namespace Ikhana.Domain.Entities;

public class TaxStatus : BaseEntity, ISoftDelete, IAuditable
{
    public string Code { get; set; } = string.Empty;
    public string Name { get; set; } = string.Empty;
    public string? Description { get; set; }
    public bool IsActive { get; set; } = true;
    public DateTime? DeletedAt { get; set; }

    public ICollection<Provider> Providers { get; set; } = new List<Provider>();
}

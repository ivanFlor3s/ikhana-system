namespace Ikhana.Domain.Entities;

public class Category : BaseEntity, ISoftDelete, IAuditable
{
    public string Name { get; set; } = string.Empty;
    public string? Description { get; set; }
    public DateTime? DeletedAt { get; set; }
}

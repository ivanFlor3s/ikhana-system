namespace Ikhana.Domain.Entities;

public interface ISoftDelete
{
    DateTime? DeletedAt { get; set; }
}

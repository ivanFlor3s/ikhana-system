namespace Ikhana.Domain.Entities;

public class ProviderInventory : BaseEntity
{
    public long ProviderId { get; set; }
    public int CoilsCount { get; set; }

    public Provider Provider { get; set; } = null!;
}

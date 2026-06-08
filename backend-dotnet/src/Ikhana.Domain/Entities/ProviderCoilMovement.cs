using Ikhana.Domain.Enums;

namespace Ikhana.Domain.Entities;

public class ProviderCoilMovement : BaseEntity
{
    public long ProviderId { get; set; }
    public long? RawMaterialEntryId { get; set; }

    public MovementType Type { get; set; } = MovementType.Entry;

    public int CoilsReceived { get; set; }
    public int CoilsReturned { get; set; }

    public Provider Provider { get; set; } = null!;
    public RawMaterialEntry? Entry { get; set; }
}

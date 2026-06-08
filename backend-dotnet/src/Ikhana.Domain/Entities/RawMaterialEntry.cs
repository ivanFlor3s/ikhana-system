using System.ComponentModel.DataAnnotations.Schema;
using Ikhana.Domain.Enums;

namespace Ikhana.Domain.Entities;

public class RawMaterialEntry : BaseEntity, ISoftDelete, IAuditable
{
    public long RawMaterialTypeId { get; set; }
    public long ProviderId { get; set; }
    public long RawMaterialCharacteristicId { get; set; }

    public int? EntryNumber { get; set; }
    public string? Remito { get; set; }
    public int? Batch { get; set; }
    public DateOnly EntryDate { get; set; }
    public decimal? QuantityKg { get; set; }
    public int? CoilsCount { get; set; }

    public EntryStatus Status { get; set; } = EntryStatus.Pending;

    public string? Observations { get; set; }

    public DateTime? DeletedAt { get; set; }

    public RawMaterialType RawMaterialType { get; set; } = null!;
    public Provider Provider { get; set; } = null!;
    public RawMaterialCharacteristic RawMaterialCharacteristic { get; set; } = null!;

    public MaterialTest? Test { get; set; }
    public ICollection<ProviderCoilMovement> CoilMovements { get; set; } = new List<ProviderCoilMovement>();

    [NotMapped]
    public int ReturnedCoilsCount => CoilMovements?.Sum(c => c.CoilsReturned) ?? 0;
}

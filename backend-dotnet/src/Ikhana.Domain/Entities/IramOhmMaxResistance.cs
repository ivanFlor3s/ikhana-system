namespace Ikhana.Domain.Entities;

public class IramOhmMaxResistance : BaseEntity
{
    public long RawMaterialCharacteristicId { get; set; }
    public decimal MaxResistanceOhmKm { get; set; }

    public RawMaterialCharacteristic RawMaterialCharacteristic { get; set; } = null!;
}

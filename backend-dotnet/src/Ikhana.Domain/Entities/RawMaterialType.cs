namespace Ikhana.Domain.Entities;

public class RawMaterialType : BaseEntity, ISoftDelete
{
    public string Name { get; set; } = string.Empty;
    public DateTime? DeletedAt { get; set; }

    public ICollection<RawMaterialCharacteristic> Characteristics { get; set; } = new List<RawMaterialCharacteristic>();
}

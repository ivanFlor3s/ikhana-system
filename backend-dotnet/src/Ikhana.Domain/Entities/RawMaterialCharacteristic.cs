using System.ComponentModel.DataAnnotations.Schema;

namespace Ikhana.Domain.Entities;

public class RawMaterialCharacteristic : BaseEntity, ISoftDelete
{
    public long RawMaterialTypeId { get; set; }
    public string Name { get; set; } = string.Empty;
    public decimal? DecimalValue { get; set; }
    public string? TextValue { get; set; }
    public string? Unit { get; set; }
    public DateTime? DeletedAt { get; set; }

    public RawMaterialType RawMaterialType { get; set; } = null!;
    public IramOhmMaxResistance? IramOhmMaxResistance { get; set; }

    [NotMapped]
    public string Description =>
        DecimalValue.HasValue
            ? $"{DecimalValue.Value:F2}{(Unit is not null ? $" {Unit}" : "")}"
            : TextValue ?? "N/A";
}

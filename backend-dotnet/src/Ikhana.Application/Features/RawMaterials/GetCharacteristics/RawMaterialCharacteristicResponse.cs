namespace Ikhana.Application.Features.RawMaterials.GetCharacteristics;

public class RawMaterialCharacteristicResponse
{
    public long Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
    public string? Unit { get; set; }
    public decimal? DecimalValue { get; set; }
    public string? TextValue { get; set; }
}

namespace Ikhana.Application.Features.RawMaterials.GetDiameters;

public class DiameterResponse
{
    public long CharacteristicId { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
    public decimal? DecimalValue { get; set; }
    public string? Unit { get; set; }
    public decimal? MaxResistanceOhmKm { get; set; }
}

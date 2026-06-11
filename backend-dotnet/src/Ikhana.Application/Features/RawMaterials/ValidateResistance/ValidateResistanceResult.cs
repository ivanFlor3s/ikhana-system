namespace Ikhana.Application.Features.RawMaterials.ValidateResistance;

public class ValidateResistanceResult
{
    public bool Valid { get; set; }
    public decimal? MaxAllowed { get; set; }
    public decimal Measured { get; set; }
    public bool HasRule { get; set; }
}

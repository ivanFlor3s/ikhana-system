using Ikhana.Domain.Enums;

namespace Ikhana.Domain.Entities;

public class MaterialTest : BaseEntity, ISoftDelete, IAuditable
{
    public long RawMaterialEntryId { get; set; }

    public DateOnly TestDate { get; set; }
    public decimal ResistanceOhmKm { get; set; }
    public decimal? ElongationPct { get; set; }

    public bool? CheckWinding { get; set; }
    public bool? CheckCleanliness { get; set; }
    public bool? CheckPackaging { get; set; }
    public bool? CheckIdentification { get; set; }

    public TestResult Result { get; set; }

    public string? ConductedBy { get; set; }
    public string? ApprovedBy { get; set; }

    public DateTime? DeletedAt { get; set; }

    public RawMaterialEntry Entry { get; set; } = null!;
}

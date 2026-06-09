namespace Ikhana.Application.Common.Models;

public class AgreementResponse
{
    public long Id { get; set; }
    public string Code { get; set; } = string.Empty;
    public string Name { get; set; } = string.Empty;
    public string? Description { get; set; }
    public bool IsActive { get; set; }
    public DateTime CreatedAt { get; set; }
    public DateTime UpdatedAt { get; set; }
    public DateTime? DeletedAt { get; set; }
}

public class AgreementDetailResponse : AgreementResponse
{
    public int ProvidersCount { get; set; }
}

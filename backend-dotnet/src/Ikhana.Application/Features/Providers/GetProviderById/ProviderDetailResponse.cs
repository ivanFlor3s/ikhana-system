namespace Ikhana.Application.Features.Providers;

public class ProviderDetailResponse
{
    public long Id { get; set; }
    public string FantasyName { get; set; } = string.Empty;
    public string BusinessName { get; set; } = string.Empty;
    public string? Cuit { get; set; }
    public string? Iibb { get; set; }
    public TaxStatusInfo? TaxStatus { get; set; }
    public AgreementInfo? Agreement { get; set; }
    public List<CategoryInfo> Categories { get; set; } = new();
    public List<BrokerInfo> Brokers { get; set; } = new();
    public string? Phone { get; set; }
    public string? Email { get; set; }
    public string? Address { get; set; }
    public string? Website { get; set; }
    public string? ContactName { get; set; }
    public string? Observations { get; set; }
    public string? BusinessHoursStart { get; set; }
    public string? BusinessHoursEnd { get; set; }
    public DateTime CreatedAt { get; set; }
    public DateTime UpdatedAt { get; set; }
}

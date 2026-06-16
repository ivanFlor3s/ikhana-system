namespace Ikhana.Application.Features.Providers;

public class ProviderListResponse
{
    public long Id { get; set; }
    public string FantasyName { get; set; } = string.Empty;
    public string BusinessName { get; set; } = string.Empty;
    public string? Cuit { get; set; }
    public TaxStatusInfo? TaxStatus { get; set; }
    public AgreementInfo? Agreement { get; set; }
    public List<CategoryInfo> Categories { get; set; } = new();
    public List<BrokerInfo> Brokers { get; set; } = new();
    public string? Phone { get; set; }
    public string? Email { get; set; }
    public DateTime CreatedAt { get; set; }
}

public class TaxStatusInfo
{
    public long Id { get; set; }
    public string Name { get; set; } = string.Empty;
}

public class AgreementInfo
{
    public long Id { get; set; }
    public string Name { get; set; } = string.Empty;
}

public class CategoryInfo
{
    public long Id { get; set; }
    public string Name { get; set; } = string.Empty;
}

public class BrokerInfo
{
    public long Id { get; set; }
    public string FullName { get; set; } = string.Empty;
}

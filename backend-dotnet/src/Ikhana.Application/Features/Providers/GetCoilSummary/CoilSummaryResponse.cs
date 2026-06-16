namespace Ikhana.Application.Features.Providers;

public class CoilSummaryResponse
{
    public long ProviderId { get; set; }
    public string ProviderName { get; set; } = string.Empty;
    public int CoilsCount { get; set; }
    public DateTime? LastMovementIn { get; set; }
    public DateTime? LastMovementOut { get; set; }
}

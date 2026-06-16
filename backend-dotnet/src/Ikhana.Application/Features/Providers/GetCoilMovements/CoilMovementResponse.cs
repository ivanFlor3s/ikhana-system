namespace Ikhana.Application.Features.Providers;

public class CoilMovementResponse
{
    public long Id { get; set; }
    public DateTime? Date { get; set; }
    public int CoilsReceived { get; set; }
    public int CoilsReturned { get; set; }
    public EntryInfo? Entry { get; set; }
}

public class EntryInfo
{
    public long Id { get; set; }
    public int? EntryNumber { get; set; }
    public string? Remito { get; set; }
}

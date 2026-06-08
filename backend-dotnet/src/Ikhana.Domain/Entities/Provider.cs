namespace Ikhana.Domain.Entities;

public class Provider : BaseEntity, ISoftDelete, IAuditable
{
    public string? FantasyName { get; set; }
    public string? BusinessName { get; set; }
    public string? Cuit { get; set; }
    public string? Iibb { get; set; }

    public long? TaxStatusId { get; set; }
    public long? AgreementId { get; set; }

    public string? Address { get; set; }
    public string? Website { get; set; }
    public string? ContactName { get; set; }
    public string? Observations { get; set; }

    public TimeOnly? BusinessHoursStart { get; set; }
    public TimeOnly? BusinessHoursEnd { get; set; }

    public DateTime? DeletedAt { get; set; }

    public TaxStatus? TaxStatus { get; set; }
    public Agreement? Agreement { get; set; }

    public ICollection<Category> Categories { get; set; } = new List<Category>();
    public ICollection<Broker> Brokers { get; set; } = new List<Broker>();
}

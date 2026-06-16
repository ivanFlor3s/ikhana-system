using MediatR;

namespace Ikhana.Application.Features.Providers;

public record UpdateProviderCommand(
    long Id,
    string? FantasyName,
    string? BusinessName,
    string? Cuit,
    string? Iibb,
    long? TaxStatusId,
    long? AgreementId,
    List<long>? CategoryIds,
    List<long>? BrokerIds,
    string? Phone,
    string? Email,
    string? Address,
    string? Website,
    string? ContactName,
    string? Observations,
    string? BusinessHoursStart,
    string? BusinessHoursEnd
) : IRequest<ProviderDetailResponse?>;

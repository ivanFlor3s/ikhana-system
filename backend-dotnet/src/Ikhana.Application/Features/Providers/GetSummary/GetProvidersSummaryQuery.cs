using MediatR;

namespace Ikhana.Application.Features.Providers.GetSummary;

public record GetProvidersSummaryQuery : IRequest<List<ProviderSummaryResponse>>;

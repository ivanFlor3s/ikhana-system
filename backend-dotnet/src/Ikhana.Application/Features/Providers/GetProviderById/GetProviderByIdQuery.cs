using MediatR;

namespace Ikhana.Application.Features.Providers;

public record GetProviderByIdQuery(long Id) : IRequest<ProviderDetailResponse?>;

using MediatR;

namespace Ikhana.Application.Features.Providers;

public record GetProviderInventoryQuery(long ProviderId) : IRequest<ProviderInventoryResponse>;

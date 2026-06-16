using MediatR;

namespace Ikhana.Application.Features.Providers;

public record CreateProviderInventoryCommand(long ProviderId, int CoilsAmount) : IRequest<ProviderInventoryResponse>;

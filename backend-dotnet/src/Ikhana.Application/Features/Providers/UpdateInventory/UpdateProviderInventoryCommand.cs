using MediatR;

namespace Ikhana.Application.Features.Providers;

public record UpdateProviderInventoryCommand(long ProviderId, int CoilsAmount) : IRequest<ProviderInventoryResponse?>;

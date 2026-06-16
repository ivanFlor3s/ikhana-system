using MediatR;

namespace Ikhana.Application.Features.Providers;

public record DeleteProviderCommand(long Id) : IRequest<bool>;

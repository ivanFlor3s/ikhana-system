using MediatR;

namespace Ikhana.Application.Features.Brokers;

public record DeleteBrokerCommand(long Id) : IRequest<bool>;

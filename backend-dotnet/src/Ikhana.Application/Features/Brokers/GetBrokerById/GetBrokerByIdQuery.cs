using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Brokers;

public record GetBrokerByIdQuery(long Id) : IRequest<BrokerResponse?>;

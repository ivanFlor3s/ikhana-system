using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Brokers;

public record UpdateBrokerCommand(long Id, string? FirstName, string? LastName, string? Email, string? Phone) : IRequest<BrokerResponse?>;

using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Providers;

public record CreateProviderBrokerCommand(
    long ProviderId,
    string FirstName,
    string LastName,
    string Email,
    string? Phone
) : IRequest<BrokerResponse>;

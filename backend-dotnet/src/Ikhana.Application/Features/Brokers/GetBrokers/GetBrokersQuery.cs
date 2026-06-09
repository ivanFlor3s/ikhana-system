using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Brokers;

public record GetBrokersQuery(
    int Page = 1,
    int PageSize = 10,
    string SortBy = "last_name",
    string SortOrder = "asc"
) : IRequest<PaginatedList<BrokerResponse>>;

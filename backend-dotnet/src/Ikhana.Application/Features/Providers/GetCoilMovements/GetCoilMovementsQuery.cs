using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Providers;

public record GetCoilMovementsQuery(
    long ProviderId,
    int Page = 1,
    int PageSize = 15,
    string? DateFrom = null,
    string? DateTo = null,
    string SortBy = "date",
    string SortDir = "desc"
) : IRequest<PaginatedList<CoilMovementResponse>>;

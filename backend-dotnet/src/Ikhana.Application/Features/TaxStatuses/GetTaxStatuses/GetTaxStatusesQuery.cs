using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.TaxStatuses;

public record GetTaxStatusesQuery(
    int Page = 1,
    int PageSize = 10,
    string SortBy = "name",
    string SortOrder = "asc"
) : IRequest<PaginatedList<TaxStatusResponse>>;

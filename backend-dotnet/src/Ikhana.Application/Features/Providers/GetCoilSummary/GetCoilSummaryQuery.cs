using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Providers;

public record GetCoilSummaryQuery(
    int Page = 1,
    int PageSize = 15,
    string? Search = null,
    string SortBy = "provider_name",
    string SortDir = "asc"
) : IRequest<PaginatedList<CoilSummaryResponse>>;

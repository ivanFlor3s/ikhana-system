using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Providers;

public record GetProvidersQuery(
    int Page = 1,
    int PageSize = 15,
    string? Search = null,
    long? CategoryId = null
) : IRequest<PaginatedList<ProviderListResponse>>;

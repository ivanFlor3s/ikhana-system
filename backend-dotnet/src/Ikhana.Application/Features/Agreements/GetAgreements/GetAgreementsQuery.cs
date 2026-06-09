using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Agreements;

public record GetAgreementsQuery(
    int Page = 1,
    int PageSize = 10,
    string SortBy = "name",
    string SortOrder = "asc"
) : IRequest<PaginatedList<AgreementResponse>>;

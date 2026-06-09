using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.TaxStatuses;

public record GetTaxStatusByIdQuery(long Id) : IRequest<TaxStatusDetailResponse?>;

using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.TaxStatuses;

public record UpdateTaxStatusCommand(long Id, string? Code, string? Name, string? Description, bool? IsActive) : IRequest<TaxStatusResponse?>;

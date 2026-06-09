using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.TaxStatuses;

public record CreateTaxStatusCommand(string Code, string Name, string? Description, bool IsActive) : IRequest<TaxStatusResponse>;

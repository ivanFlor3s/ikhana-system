using MediatR;

namespace Ikhana.Application.Features.TaxStatuses;

public record DeleteTaxStatusCommand(long Id) : IRequest<bool>;

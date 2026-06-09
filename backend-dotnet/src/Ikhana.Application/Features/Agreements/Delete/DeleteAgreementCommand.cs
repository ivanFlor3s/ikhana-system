using MediatR;

namespace Ikhana.Application.Features.Agreements;

public record DeleteAgreementCommand(long Id) : IRequest<bool>;

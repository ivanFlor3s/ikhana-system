using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Agreements;

public record UpdateAgreementCommand(long Id, string? Code, string? Name, string? Description, bool? IsActive) : IRequest<AgreementResponse?>;

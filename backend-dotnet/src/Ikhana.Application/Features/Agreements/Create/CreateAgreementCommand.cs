using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Agreements;

public record CreateAgreementCommand(string Code, string Name, string? Description, bool IsActive) : IRequest<AgreementResponse>;

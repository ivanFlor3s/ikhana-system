using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Agreements;

public record GetAgreementByIdQuery(long Id) : IRequest<AgreementDetailResponse?>;

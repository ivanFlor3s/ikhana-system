using MediatR;

namespace Ikhana.Application.Features.RawMaterials.GetDiameters;

public record GetDiametersQuery : IRequest<List<DiameterResponse>>;

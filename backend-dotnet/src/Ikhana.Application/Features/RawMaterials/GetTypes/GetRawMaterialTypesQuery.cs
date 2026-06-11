using MediatR;

namespace Ikhana.Application.Features.RawMaterials.GetTypes;

public record GetRawMaterialTypesQuery : IRequest<List<RawMaterialTypeResponse>>;

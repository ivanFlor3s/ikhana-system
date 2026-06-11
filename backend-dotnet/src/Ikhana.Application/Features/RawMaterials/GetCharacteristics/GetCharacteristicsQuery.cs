using MediatR;

namespace Ikhana.Application.Features.RawMaterials.GetCharacteristics;

public record GetCharacteristicsQuery(long TypeId) : IRequest<List<RawMaterialCharacteristicResponse>>;

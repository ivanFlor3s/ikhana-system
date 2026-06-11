using MediatR;

namespace Ikhana.Application.Features.RawMaterials.ValidateResistance;

public record ValidateResistanceCommand(long RawMaterialCharacteristicId, decimal ResistanceOhmKm) : IRequest<ValidateResistanceResult>;

using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.RawMaterials.ValidateResistance;

public class ValidateResistanceHandler : IRequestHandler<ValidateResistanceCommand, ValidateResistanceResult>
{
    private readonly IAppDbContext _context;

    public ValidateResistanceHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<ValidateResistanceResult> Handle(ValidateResistanceCommand request, CancellationToken cancellationToken)
    {
        var characteristic = await _context.RawMaterialCharacteristics
            .Include(c => c.IramOhmMaxResistance)
            .FirstAsync(c => c.Id == request.RawMaterialCharacteristicId, cancellationToken);

        var rule = characteristic.IramOhmMaxResistance;

        if (rule == null)
        {
            return new ValidateResistanceResult
            {
                Valid = true,
                MaxAllowed = null,
                Measured = request.ResistanceOhmKm,
                HasRule = false
            };
        }

        var isValid = request.ResistanceOhmKm <= rule.MaxResistanceOhmKm;

        return new ValidateResistanceResult
        {
            Valid = isValid,
            MaxAllowed = rule.MaxResistanceOhmKm,
            Measured = request.ResistanceOhmKm,
            HasRule = true
        };
    }
}

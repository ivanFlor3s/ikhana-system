using FluentValidation;
using Ikhana.Application.Common.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.RawMaterials.ValidateResistance;

public class ValidateResistanceValidator : AbstractValidator<ValidateResistanceCommand>
{
    private readonly IAppDbContext _context;

    public ValidateResistanceValidator(IAppDbContext context)
    {
        _context = context;

        RuleFor(x => x.RawMaterialCharacteristicId)
            .MustAsync(CharacteristicExists).WithMessage("The characteristic does not exist.");

        RuleFor(x => x.ResistanceOhmKm)
            .GreaterThanOrEqualTo(0).WithMessage("The resistance must be greater than or equal to 0.");
    }

    private async Task<bool> CharacteristicExists(long id, CancellationToken cancellationToken)
    {
        return await _context.RawMaterialCharacteristics
            .AnyAsync(c => c.Id == id && c.DeletedAt == null, cancellationToken);
    }
}

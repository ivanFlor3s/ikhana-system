using FluentValidation;
using Ikhana.Application.Common.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.TaxStatuses;

public class UpdateTaxStatusValidator : AbstractValidator<UpdateTaxStatusCommand>
{
    private readonly IAppDbContext _context;

    public UpdateTaxStatusValidator(IAppDbContext context)
    {
        _context = context;

        RuleFor(x => x.Code)
            .MaximumLength(10).WithMessage("The code may not be greater than 10 characters.")
            .MustAsync(BeUniqueCodeExceptSelf).WithMessage("The code has already been taken.")
            .When(x => x.Code != null);

        RuleFor(x => x.Name)
            .MaximumLength(128).WithMessage("The name may not be greater than 128 characters.")
            .When(x => x.Name != null);

        RuleFor(x => x.Description)
            .MaximumLength(512).WithMessage("The description may not be greater than 512 characters.")
            .When(x => x.Description != null);
    }

    private async Task<bool> BeUniqueCodeExceptSelf(UpdateTaxStatusCommand command, string? code, CancellationToken cancellationToken)
    {
        if (string.IsNullOrEmpty(code))
            return true;

        return !await _context.TaxStatuses
            .AnyAsync(t => t.Code == code && t.Id != command.Id && t.DeletedAt == null, cancellationToken);
    }
}

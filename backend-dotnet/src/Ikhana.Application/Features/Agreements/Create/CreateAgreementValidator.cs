using FluentValidation;
using Ikhana.Application.Common.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Agreements;

public class CreateAgreementValidator : AbstractValidator<CreateAgreementCommand>
{
    private readonly IAppDbContext _context;

    public CreateAgreementValidator(IAppDbContext context)
    {
        _context = context;

        RuleFor(x => x.Code)
            .NotEmpty().WithMessage("The code field is required.")
            .MaximumLength(50).WithMessage("The code may not be greater than 50 characters.")
            .MustAsync(BeUniqueCode).WithMessage("The code has already been taken.");

        RuleFor(x => x.Name)
            .NotEmpty().WithMessage("The name field is required.")
            .MaximumLength(128).WithMessage("The name may not be greater than 128 characters.");

        RuleFor(x => x.Description)
            .MaximumLength(512).WithMessage("The description may not be greater than 512 characters.");
    }

    private async Task<bool> BeUniqueCode(string code, CancellationToken cancellationToken)
    {
        return !await _context.Agreements
            .AnyAsync(a => a.Code == code && a.DeletedAt == null, cancellationToken);
    }
}

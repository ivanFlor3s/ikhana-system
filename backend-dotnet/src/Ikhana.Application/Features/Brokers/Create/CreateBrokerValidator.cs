using FluentValidation;
using Ikhana.Application.Common.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Brokers;

public class CreateBrokerValidator : AbstractValidator<CreateBrokerCommand>
{
    private readonly IAppDbContext _context;

    public CreateBrokerValidator(IAppDbContext context)
    {
        _context = context;

        RuleFor(x => x.FirstName)
            .NotEmpty().WithMessage("The first name field is required.")
            .MaximumLength(128).WithMessage("The first name may not be greater than 128 characters.");

        RuleFor(x => x.LastName)
            .NotEmpty().WithMessage("The last name field is required.")
            .MaximumLength(128).WithMessage("The last name may not be greater than 128 characters.");

        RuleFor(x => x.Email)
            .NotEmpty().WithMessage("The email field is required.")
            .MaximumLength(256).WithMessage("The email may not be greater than 256 characters.")
            .EmailAddress().WithMessage("The email must be a valid email address.")
            .MustAsync(BeUniqueEmail).WithMessage("The email has already been taken.");

        RuleFor(x => x.Phone)
            .MaximumLength(32).WithMessage("The phone may not be greater than 32 characters.");
    }

    private async Task<bool> BeUniqueEmail(string email, CancellationToken cancellationToken)
    {
        return !await _context.Brokers
            .AnyAsync(b => b.Email == email && b.DeletedAt == null, cancellationToken);
    }
}

using FluentValidation;
using Ikhana.Application.Common.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Brokers;

public class UpdateBrokerValidator : AbstractValidator<UpdateBrokerCommand>
{
    private readonly IAppDbContext _context;

    public UpdateBrokerValidator(IAppDbContext context)
    {
        _context = context;

        RuleFor(x => x.FirstName)
            .MaximumLength(128).WithMessage("The first name may not be greater than 128 characters.")
            .When(x => x.FirstName != null);

        RuleFor(x => x.LastName)
            .MaximumLength(128).WithMessage("The last name may not be greater than 128 characters.")
            .When(x => x.LastName != null);

        RuleFor(x => x.Email)
            .MaximumLength(256).WithMessage("The email may not be greater than 256 characters.")
            .EmailAddress().WithMessage("The email must be a valid email address.")
            .MustAsync(BeUniqueEmailExceptSelf).WithMessage("The email has already been taken.")
            .When(x => x.Email != null);

        RuleFor(x => x.Phone)
            .MaximumLength(32).WithMessage("The phone may not be greater than 32 characters.")
            .When(x => x.Phone != null);
    }

    private async Task<bool> BeUniqueEmailExceptSelf(UpdateBrokerCommand command, string? email, CancellationToken cancellationToken)
    {
        if (string.IsNullOrEmpty(email))
            return true;

        return !await _context.Brokers
            .AnyAsync(b => b.Email == email && b.Id != command.Id && b.DeletedAt == null, cancellationToken);
    }
}

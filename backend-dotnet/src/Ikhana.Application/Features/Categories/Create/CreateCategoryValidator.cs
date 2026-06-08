using FluentValidation;
using Ikhana.Application.Common.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Categories;

public class CreateCategoryValidator : AbstractValidator<CreateCategoryCommand>
{
    private readonly IAppDbContext _context;

    public CreateCategoryValidator(IAppDbContext context)
    {
        _context = context;

        RuleFor(x => x.Name)
            .NotEmpty().WithMessage("The name field is required.")
            .MaximumLength(128).WithMessage("The name may not be greater than 128 characters.")
            .MustAsync(BeUniqueName).WithMessage("The name has already been taken.");

        RuleFor(x => x.Description)
            .MaximumLength(512).WithMessage("The description may not be greater than 512 characters.");
    }

    private async Task<bool> BeUniqueName(string name, CancellationToken cancellationToken)
    {
        return !await _context.Categories
            .AnyAsync(c => c.Name == name && c.DeletedAt == null, cancellationToken);
    }
}

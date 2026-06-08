using FluentValidation;
using Ikhana.Application.Common.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Categories;

public class UpdateCategoryValidator : AbstractValidator<UpdateCategoryCommand>
{
    private readonly IAppDbContext _context;

    public UpdateCategoryValidator(IAppDbContext context)
    {
        _context = context;

        RuleFor(x => x.Name)
            .MaximumLength(128).WithMessage("The name may not be greater than 128 characters.")
            .MustAsync(BeUniqueNameExceptSelf).WithMessage("The name has already been taken.")
            .When(x => x.Name != null);

        RuleFor(x => x.Description)
            .MaximumLength(512).WithMessage("The description may not be greater than 512 characters.")
            .When(x => x.Description != null);
    }

    private async Task<bool> BeUniqueNameExceptSelf(UpdateCategoryCommand command, string? name, CancellationToken cancellationToken)
    {
        if (string.IsNullOrEmpty(name))
            return true;

        return !await _context.Categories
            .AnyAsync(c => c.Name == name && c.Id != command.Id && c.DeletedAt == null, cancellationToken);
    }
}

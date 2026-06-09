using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Categories;

public class UpdateCategoryHandler : IRequestHandler<UpdateCategoryCommand, CategoryResponse?>
{
    private readonly IAppDbContext _context;

    public UpdateCategoryHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<CategoryResponse?> Handle(UpdateCategoryCommand request, CancellationToken cancellationToken)
    {
        var category = await _context.Categories
            .FirstOrDefaultAsync(c => c.Id == request.Id && c.DeletedAt == null, cancellationToken);

        if (category == null)
            return null;

        if (request.Name != null)
            category.Name = request.Name;

        if (request.Description != null)
            category.Description = request.Description;

        await _context.SaveChangesAsync(cancellationToken);

        return new CategoryResponse
        {
            Id = category.Id,
            Name = category.Name,
            Description = category.Description,
            CreatedAt = category.CreatedAt,
            UpdatedAt = category.UpdatedAt,
            DeletedAt = category.DeletedAt
        };
    }
}

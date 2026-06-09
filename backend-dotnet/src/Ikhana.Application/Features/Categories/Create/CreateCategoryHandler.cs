using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using Ikhana.Domain.Entities;
using MediatR;

namespace Ikhana.Application.Features.Categories;

public class CreateCategoryHandler : IRequestHandler<CreateCategoryCommand, CategoryResponse>
{
    private readonly IAppDbContext _context;

    public CreateCategoryHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<CategoryResponse> Handle(CreateCategoryCommand request, CancellationToken cancellationToken)
    {
        var category = new Category
        {
            Name = request.Name,
            Description = request.Description
        };

        _context.Categories.Add(category);
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

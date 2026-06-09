using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Categories;

public class GetCategoryByIdHandler : IRequestHandler<GetCategoryByIdQuery, CategoryDetailResponse?>
{
    private readonly IAppDbContext _context;

    public GetCategoryByIdHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<CategoryDetailResponse?> Handle(GetCategoryByIdQuery request, CancellationToken cancellationToken)
    {
        return await _context.Categories
            .Where(c => c.Id == request.Id && c.DeletedAt == null)
            .Select(c => new CategoryDetailResponse
            {
                Id = c.Id,
                Name = c.Name,
                Description = c.Description,
                CreatedAt = c.CreatedAt,
                UpdatedAt = c.UpdatedAt,
                DeletedAt = c.DeletedAt,
                ProvidersCount = c.Providers.Count
            })
            .FirstOrDefaultAsync(cancellationToken);
    }
}

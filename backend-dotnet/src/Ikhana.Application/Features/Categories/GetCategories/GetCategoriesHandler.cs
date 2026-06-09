using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Categories;

public class GetCategoriesHandler : IRequestHandler<GetCategoriesQuery, PaginatedList<CategoryResponse>>
{
    private readonly IAppDbContext _context;

    public GetCategoriesHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<PaginatedList<CategoryResponse>> Handle(GetCategoriesQuery request, CancellationToken cancellationToken)
    {
        var query = _context.Categories.Where(c => c.DeletedAt == null);

        query = request.SortBy.ToLower() switch
        {
            "created_at" => request.SortOrder.ToLower() == "desc"
                ? query.OrderByDescending(c => c.CreatedAt)
                : query.OrderBy(c => c.CreatedAt),
            _ => request.SortOrder.ToLower() == "desc"
                ? query.OrderByDescending(c => c.Name)
                : query.OrderBy(c => c.Name)
        };

        var totalCount = await query.CountAsync(cancellationToken);

        var items = await query
            .Skip((request.Page - 1) * request.PageSize)
            .Take(request.PageSize)
            .Select(c => new CategoryResponse
            {
                Id = c.Id,
                Name = c.Name,
                Description = c.Description,
                CreatedAt = c.CreatedAt,
                UpdatedAt = c.UpdatedAt,
                DeletedAt = c.DeletedAt
            })
            .ToListAsync(cancellationToken);

        return new PaginatedList<CategoryResponse>
        {
            Items = items,
            Page = request.Page,
            PageSize = request.PageSize,
            TotalCount = totalCount
        };
    }
}

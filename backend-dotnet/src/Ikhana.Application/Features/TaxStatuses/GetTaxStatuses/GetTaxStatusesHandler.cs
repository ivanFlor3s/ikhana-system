using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.TaxStatuses;

public class GetTaxStatusesHandler : IRequestHandler<GetTaxStatusesQuery, PaginatedList<TaxStatusResponse>>
{
    private readonly IAppDbContext _context;

    public GetTaxStatusesHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<PaginatedList<TaxStatusResponse>> Handle(GetTaxStatusesQuery request, CancellationToken cancellationToken)
    {
        var query = _context.TaxStatuses.Where(t => t.DeletedAt == null);

        query = request.SortBy.ToLower() switch
        {
            "created_at" => request.SortOrder.ToLower() == "desc"
                ? query.OrderByDescending(t => t.CreatedAt)
                : query.OrderBy(t => t.CreatedAt),
            _ => request.SortOrder.ToLower() == "desc"
                ? query.OrderByDescending(t => t.Name)
                : query.OrderBy(t => t.Name)
        };

        var totalCount = await query.CountAsync(cancellationToken);

        var items = await query
            .Skip((request.Page - 1) * request.PageSize)
            .Take(request.PageSize)
            .Select(t => new TaxStatusResponse
            {
                Id = t.Id,
                Code = t.Code,
                Name = t.Name,
                Description = t.Description,
                IsActive = t.IsActive,
                CreatedAt = t.CreatedAt,
                UpdatedAt = t.UpdatedAt,
                DeletedAt = t.DeletedAt
            })
            .ToListAsync(cancellationToken);

        return new PaginatedList<TaxStatusResponse>
        {
            Items = items,
            Page = request.Page,
            PageSize = request.PageSize,
            TotalCount = totalCount
        };
    }
}

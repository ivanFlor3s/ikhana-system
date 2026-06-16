using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Providers;

public class GetCoilSummaryHandler : IRequestHandler<GetCoilSummaryQuery, PaginatedList<CoilSummaryResponse>>
{
    private readonly IAppDbContext _context;

    public GetCoilSummaryHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<PaginatedList<CoilSummaryResponse>> Handle(GetCoilSummaryQuery request, CancellationToken cancellationToken)
    {
        var query = from pi in _context.ProviderInventories
                    join p in _context.Providers on pi.ProviderId equals p.Id
                    where p.DeletedAt == null
                    select new CoilSummaryResponse
                    {
                        ProviderId = p.Id,
                        ProviderName = p.FantasyName ?? p.BusinessName ?? string.Empty,
                        CoilsCount = pi.CoilsCount,
                        LastMovementIn = _context.ProviderCoilMovements
                            .Where(m => m.ProviderId == p.Id && m.CoilsReceived > 0)
                            .OrderByDescending(m => m.CreatedAt)
                            .Select(m => (DateTime?)m.CreatedAt)
                            .FirstOrDefault(),
                        LastMovementOut = _context.ProviderCoilMovements
                            .Where(m => m.ProviderId == p.Id && m.CoilsReturned > 0)
                            .OrderByDescending(m => m.CreatedAt)
                            .Select(m => (DateTime?)m.CreatedAt)
                            .FirstOrDefault(),
                    };

        if (!string.IsNullOrWhiteSpace(request.Search))
        {
            query = query.Where(r =>
                r.ProviderName.Contains(request.Search));
        }

        query = request.SortBy.ToLower() switch
        {
            "coils_count" => request.SortDir.ToLower() == "desc"
                ? query.OrderByDescending(r => r.CoilsCount)
                : query.OrderBy(r => r.CoilsCount),
            "last_movement_in" => request.SortDir.ToLower() == "desc"
                ? query.OrderByDescending(r => r.LastMovementIn)
                : query.OrderBy(r => r.LastMovementIn),
            "last_movement_out" => request.SortDir.ToLower() == "desc"
                ? query.OrderByDescending(r => r.LastMovementOut)
                : query.OrderBy(r => r.LastMovementOut),
            _ => request.SortDir.ToLower() == "desc"
                ? query.OrderByDescending(r => r.ProviderName)
                : query.OrderBy(r => r.ProviderName)
        };

        var totalCount = await query.CountAsync(cancellationToken);

        var items = await query
            .Skip((request.Page - 1) * request.PageSize)
            .Take(request.PageSize)
            .ToListAsync(cancellationToken);

        return new PaginatedList<CoilSummaryResponse>
        {
            Items = items,
            Page = request.Page,
            PageSize = request.PageSize,
            TotalCount = totalCount,
        };
    }
}

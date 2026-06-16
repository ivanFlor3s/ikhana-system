using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Providers;

public class GetCoilMovementsHandler : IRequestHandler<GetCoilMovementsQuery, PaginatedList<CoilMovementResponse>>
{
    private readonly IAppDbContext _context;

    public GetCoilMovementsHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<PaginatedList<CoilMovementResponse>> Handle(GetCoilMovementsQuery request, CancellationToken cancellationToken)
    {
        var query = _context.ProviderCoilMovements
            .Include(m => m.Entry)
            .Where(m => m.ProviderId == request.ProviderId);

        if (!string.IsNullOrWhiteSpace(request.DateFrom) && DateOnly.TryParse(request.DateFrom, out var dateFrom))
            query = query.Where(m => m.CreatedAt >= dateFrom.ToDateTime(TimeOnly.MinValue));

        if (!string.IsNullOrWhiteSpace(request.DateTo) && DateOnly.TryParse(request.DateTo, out var dateTo))
            query = query.Where(m => m.CreatedAt <= dateTo.ToDateTime(TimeOnly.MaxValue));

        query = request.SortBy.ToLower() switch
        {
            "coils_received" => request.SortDir.ToLower() == "asc"
                ? query.OrderBy(m => m.CoilsReceived)
                : query.OrderByDescending(m => m.CoilsReceived),
            "coils_returned" => request.SortDir.ToLower() == "asc"
                ? query.OrderBy(m => m.CoilsReturned)
                : query.OrderByDescending(m => m.CoilsReturned),
            _ => request.SortDir.ToLower() == "asc"
                ? query.OrderBy(m => m.CreatedAt)
                : query.OrderByDescending(m => m.CreatedAt)
        };

        var totalCount = await query.CountAsync(cancellationToken);

        var items = await query
            .Skip((request.Page - 1) * request.PageSize)
            .Take(request.PageSize)
            .Select(m => new CoilMovementResponse
            {
                Id = m.Id,
                Date = m.CreatedAt,
                CoilsReceived = m.CoilsReceived,
                CoilsReturned = m.CoilsReturned,
                Entry = m.Entry != null ? new EntryInfo
                {
                    Id = m.Entry.Id,
                    EntryNumber = m.Entry.EntryNumber,
                    Remito = m.Entry.Remito,
                } : null,
            })
            .ToListAsync(cancellationToken);

        return new PaginatedList<CoilMovementResponse>
        {
            Items = items,
            Page = request.Page,
            PageSize = request.PageSize,
            TotalCount = totalCount,
        };
    }
}

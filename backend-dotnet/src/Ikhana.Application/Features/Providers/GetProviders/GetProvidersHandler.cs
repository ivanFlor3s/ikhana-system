using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Providers;

public class GetProvidersHandler : IRequestHandler<GetProvidersQuery, PaginatedList<ProviderListResponse>>
{
    private readonly IAppDbContext _context;

    public GetProvidersHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<PaginatedList<ProviderListResponse>> Handle(GetProvidersQuery request, CancellationToken cancellationToken)
    {
        var query = _context.Providers
            .Where(p => p.DeletedAt == null);

        if (!string.IsNullOrWhiteSpace(request.Search))
        {
            query = query.Where(p =>
                (p.FantasyName != null && p.FantasyName.Contains(request.Search)) ||
                (p.BusinessName != null && p.BusinessName.Contains(request.Search)));
        }

        if (request.CategoryId.HasValue)
        {
            query = query.Where(p => p.Categories.Any(c => c.Id == request.CategoryId.Value));
        }

        var totalCount = await query.CountAsync(cancellationToken);

        var items = await query
            .OrderByDescending(p => p.CreatedAt)
            .Skip((request.Page - 1) * request.PageSize)
            .Take(request.PageSize)
            .Select(p => new ProviderListResponse
            {
                Id = p.Id,
                FantasyName = p.FantasyName ?? string.Empty,
                BusinessName = p.BusinessName ?? string.Empty,
                Cuit = p.Cuit,
                TaxStatus = p.TaxStatus != null ? new TaxStatusInfo { Id = p.TaxStatus.Id, Name = p.TaxStatus.Name } : null,
                Agreement = p.Agreement != null ? new AgreementInfo { Id = p.Agreement.Id, Name = p.Agreement.Name } : null,
                Categories = p.Categories
                    .Where(c => c.DeletedAt == null)
                    .Select(c => new CategoryInfo { Id = c.Id, Name = c.Name })
                    .ToList(),
                Brokers = p.Brokers
                    .Where(b => b.DeletedAt == null)
                    .Select(b => new BrokerInfo { Id = b.Id, FullName = b.FirstName + " " + b.LastName })
                    .ToList(),
                Phone = p.Phone,
                Email = p.Email,
                CreatedAt = p.CreatedAt,
            })
            .ToListAsync(cancellationToken);

        return new PaginatedList<ProviderListResponse>
        {
            Items = items,
            Page = request.Page,
            PageSize = request.PageSize,
            TotalCount = totalCount,
        };
    }
}

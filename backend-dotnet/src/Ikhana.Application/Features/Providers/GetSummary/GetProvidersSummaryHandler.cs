using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Providers.GetSummary;

public class GetProvidersSummaryHandler : IRequestHandler<GetProvidersSummaryQuery, List<ProviderSummaryResponse>>
{
    private readonly IAppDbContext _context;

    public GetProvidersSummaryHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<List<ProviderSummaryResponse>> Handle(GetProvidersSummaryQuery request, CancellationToken cancellationToken)
    {
        return await _context.Providers
            .Where(p => p.DeletedAt == null)
            .OrderBy(p => p.FantasyName)
            .Select(p => new ProviderSummaryResponse
            {
                Id = p.Id,
                FantasyName = p.FantasyName ?? string.Empty,
                BusinessName = p.BusinessName ?? string.Empty,
            })
            .ToListAsync(cancellationToken);
    }
}

using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Providers;

public class GetProviderByIdHandler : IRequestHandler<GetProviderByIdQuery, ProviderDetailResponse?>
{
    private readonly IAppDbContext _context;

    public GetProviderByIdHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<ProviderDetailResponse?> Handle(GetProviderByIdQuery request, CancellationToken cancellationToken)
    {
        var provider = await _context.Providers
            .Include(p => p.TaxStatus)
            .Include(p => p.Agreement)
            .Include(p => p.Categories)
            .Include(p => p.Brokers)
            .FirstOrDefaultAsync(p => p.Id == request.Id && p.DeletedAt == null, cancellationToken);

        if (provider == null)
            return null;

        return new ProviderDetailResponse
        {
            Id = provider.Id,
            FantasyName = provider.FantasyName ?? string.Empty,
            BusinessName = provider.BusinessName ?? string.Empty,
            Cuit = provider.Cuit,
            Iibb = provider.Iibb,
            TaxStatus = provider.TaxStatus != null
                ? new TaxStatusInfo { Id = provider.TaxStatus.Id, Name = provider.TaxStatus.Name }
                : null,
            Agreement = provider.Agreement != null
                ? new AgreementInfo { Id = provider.Agreement.Id, Name = provider.Agreement.Name }
                : null,
            Categories = provider.Categories
                .Where(c => c.DeletedAt == null)
                .Select(c => new CategoryInfo { Id = c.Id, Name = c.Name })
                .ToList(),
            Brokers = provider.Brokers
                .Where(b => b.DeletedAt == null)
                .Select(b => new BrokerInfo { Id = b.Id, FullName = b.FirstName + " " + b.LastName })
                .ToList(),
            Phone = provider.Phone,
            Email = provider.Email,
            Address = provider.Address,
            Website = provider.Website,
            ContactName = provider.ContactName,
            Observations = provider.Observations,
            BusinessHoursStart = provider.BusinessHoursStart?.ToString("HH:mm"),
            BusinessHoursEnd = provider.BusinessHoursEnd?.ToString("HH:mm"),
            CreatedAt = provider.CreatedAt,
            UpdatedAt = provider.UpdatedAt,
        };
    }
}

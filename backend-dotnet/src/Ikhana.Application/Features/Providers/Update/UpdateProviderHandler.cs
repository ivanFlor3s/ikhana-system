using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Providers;

public class UpdateProviderHandler : IRequestHandler<UpdateProviderCommand, ProviderDetailResponse?>
{
    private readonly IAppDbContext _context;

    public UpdateProviderHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<ProviderDetailResponse?> Handle(UpdateProviderCommand request, CancellationToken cancellationToken)
    {
        var provider = await _context.Providers
            .Include(p => p.TaxStatus)
            .Include(p => p.Agreement)
            .Include(p => p.Categories)
            .Include(p => p.Brokers)
            .FirstOrDefaultAsync(p => p.Id == request.Id && p.DeletedAt == null, cancellationToken);

        if (provider == null)
            return null;

        if (request.FantasyName != null)
            provider.FantasyName = request.FantasyName;
        if (request.BusinessName != null)
            provider.BusinessName = request.BusinessName;
        if (request.Cuit != null)
            provider.Cuit = request.Cuit;
        if (request.Iibb != null)
            provider.Iibb = request.Iibb;
        if (request.TaxStatusId.HasValue)
            provider.TaxStatusId = request.TaxStatusId;
        if (request.AgreementId.HasValue)
            provider.AgreementId = request.AgreementId;
        if (request.Phone != null)
            provider.Phone = request.Phone;
        if (request.Email != null)
            provider.Email = request.Email;
        if (request.Address != null)
            provider.Address = request.Address;
        if (request.Website != null)
            provider.Website = request.Website;
        if (request.ContactName != null)
            provider.ContactName = request.ContactName;
        if (request.Observations != null)
            provider.Observations = request.Observations;
        if (request.BusinessHoursStart != null)
            provider.BusinessHoursStart = TimeOnly.TryParseExact(request.BusinessHoursStart, "HH:mm", out var start) ? start : null;
        if (request.BusinessHoursEnd != null)
            provider.BusinessHoursEnd = TimeOnly.TryParseExact(request.BusinessHoursEnd, "HH:mm", out var end) ? end : null;

        if (request.CategoryIds != null)
        {
            provider.Categories.Clear();
            var categories = await _context.Categories
                .Where(c => request.CategoryIds.Contains(c.Id) && c.DeletedAt == null)
                .ToListAsync(cancellationToken);
            foreach (var category in categories)
                provider.Categories.Add(category);
        }

        if (request.BrokerIds != null)
        {
            var existingBrokers = await _context.Brokers
                .Where(b => b.ProviderId == provider.Id)
                .ToListAsync(cancellationToken);
            foreach (var broker in existingBrokers)
                broker.ProviderId = null;

            var newBrokers = await _context.Brokers
                .Where(b => request.BrokerIds.Contains(b.Id) && b.DeletedAt == null)
                .ToListAsync(cancellationToken);
            foreach (var broker in newBrokers)
                broker.ProviderId = provider.Id;
        }

        await _context.SaveChangesAsync(cancellationToken);

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

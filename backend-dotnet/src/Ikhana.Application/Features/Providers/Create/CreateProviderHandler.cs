using Ikhana.Application.Common.Interfaces;
using Ikhana.Domain.Entities;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Providers;

public class CreateProviderHandler : IRequestHandler<CreateProviderCommand, ProviderDetailResponse>
{
    private readonly IAppDbContext _context;

    public CreateProviderHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<ProviderDetailResponse> Handle(CreateProviderCommand request, CancellationToken cancellationToken)
    {
        var provider = new Provider
        {
            FantasyName = request.FantasyName,
            BusinessName = request.BusinessName,
            Cuit = request.Cuit,
            Iibb = request.Iibb,
            TaxStatusId = request.TaxStatusId,
            AgreementId = request.AgreementId,
            Phone = request.Phone,
            Email = request.Email,
            Address = request.Address,
            Website = request.Website,
            ContactName = request.ContactName,
            Observations = request.Observations,
            BusinessHoursStart = ParseTimeOnly(request.BusinessHoursStart),
            BusinessHoursEnd = ParseTimeOnly(request.BusinessHoursEnd),
        };

        if (request.CategoryIds is { Count: > 0 })
        {
            var categories = await _context.Categories
                .Where(c => request.CategoryIds.Contains(c.Id) && c.DeletedAt == null)
                .ToListAsync(cancellationToken);
            foreach (var category in categories)
                provider.Categories.Add(category);
        }

        if (request.BrokerIds is { Count: > 0 })
        {
            var brokers = await _context.Brokers
                .Where(b => request.BrokerIds.Contains(b.Id) && b.DeletedAt == null)
                .ToListAsync(cancellationToken);
            foreach (var broker in brokers)
                broker.ProviderId = provider.Id;
        }

        _context.Providers.Add(provider);
        await _context.SaveChangesAsync(cancellationToken);

        var taxStatus = provider.TaxStatusId.HasValue
            ? await _context.TaxStatuses.FirstOrDefaultAsync(t => t.Id == provider.TaxStatusId.Value, cancellationToken)
            : null;
        var agreement = provider.AgreementId.HasValue
            ? await _context.Agreements.FirstOrDefaultAsync(a => a.Id == provider.AgreementId.Value, cancellationToken)
            : null;
        var loadedCategories = await _context.Categories
            .Where(c => provider.Categories.Select(pc => pc.Id).Contains(c.Id))
            .ToListAsync(cancellationToken);
        var loadedBrokers = await _context.Brokers
            .Where(b => b.ProviderId == provider.Id)
            .ToListAsync(cancellationToken);

        return new ProviderDetailResponse
        {
            Id = provider.Id,
            FantasyName = provider.FantasyName ?? string.Empty,
            BusinessName = provider.BusinessName ?? string.Empty,
            Cuit = provider.Cuit,
            Iibb = provider.Iibb,
            TaxStatus = taxStatus != null ? new TaxStatusInfo { Id = taxStatus.Id, Name = taxStatus.Name } : null,
            Agreement = agreement != null ? new AgreementInfo { Id = agreement.Id, Name = agreement.Name } : null,
            Categories = loadedCategories
                .Where(c => c.DeletedAt == null)
                .Select(c => new CategoryInfo { Id = c.Id, Name = c.Name })
                .ToList(),
            Brokers = loadedBrokers
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

    private static TimeOnly? ParseTimeOnly(string? time)
    {
        if (string.IsNullOrWhiteSpace(time))
            return null;
        return TimeOnly.TryParseExact(time, "HH:mm", out var result) ? result : null;
    }
}

using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Providers;

public class GetProviderInventoryHandler : IRequestHandler<GetProviderInventoryQuery, ProviderInventoryResponse>
{
    private readonly IAppDbContext _context;

    public GetProviderInventoryHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<ProviderInventoryResponse> Handle(GetProviderInventoryQuery request, CancellationToken cancellationToken)
    {
        var inventory = await _context.ProviderInventories
            .FirstOrDefaultAsync(i => i.ProviderId == request.ProviderId, cancellationToken);

        if (inventory != null)
        {
            return new ProviderInventoryResponse
            {
                Id = inventory.Id,
                ProviderId = inventory.ProviderId,
                CoilsCount = inventory.CoilsCount,
            };
        }

        var provider = await _context.Providers
            .FirstOrDefaultAsync(p => p.Id == request.ProviderId && p.DeletedAt == null, cancellationToken);

        if (provider == null)
            throw new InvalidOperationException("Provider not found");

        var newInventory = new Domain.Entities.ProviderInventory
        {
            ProviderId = request.ProviderId,
            CoilsCount = 0,
        };

        _context.ProviderInventories.Add(newInventory);
        await _context.SaveChangesAsync(cancellationToken);

        return new ProviderInventoryResponse
        {
            Id = newInventory.Id,
            ProviderId = newInventory.ProviderId,
            CoilsCount = newInventory.CoilsCount,
        };
    }
}

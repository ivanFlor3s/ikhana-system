using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Providers;

public class CreateProviderInventoryHandler : IRequestHandler<CreateProviderInventoryCommand, ProviderInventoryResponse>
{
    private readonly IAppDbContext _context;

    public CreateProviderInventoryHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<ProviderInventoryResponse> Handle(CreateProviderInventoryCommand request, CancellationToken cancellationToken)
    {
        var exists = await _context.ProviderInventories
            .AnyAsync(i => i.ProviderId == request.ProviderId, cancellationToken);

        if (exists)
            throw new InvalidOperationException("El proveedor ya tiene un inventario de bobinas");

        var inventory = new Domain.Entities.ProviderInventory
        {
            ProviderId = request.ProviderId,
            CoilsCount = request.CoilsAmount,
        };

        _context.ProviderInventories.Add(inventory);
        await _context.SaveChangesAsync(cancellationToken);

        return new ProviderInventoryResponse
        {
            Id = inventory.Id,
            ProviderId = inventory.ProviderId,
            CoilsCount = inventory.CoilsCount,
        };
    }
}

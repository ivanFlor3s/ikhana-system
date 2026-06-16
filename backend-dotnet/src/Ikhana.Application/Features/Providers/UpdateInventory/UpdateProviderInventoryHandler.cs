using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Providers;

public class UpdateProviderInventoryHandler : IRequestHandler<UpdateProviderInventoryCommand, ProviderInventoryResponse?>
{
    private readonly IAppDbContext _context;

    public UpdateProviderInventoryHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<ProviderInventoryResponse?> Handle(UpdateProviderInventoryCommand request, CancellationToken cancellationToken)
    {
        var inventory = await _context.ProviderInventories
            .FirstOrDefaultAsync(i => i.ProviderId == request.ProviderId, cancellationToken);

        if (inventory == null)
            return null;

        inventory.CoilsCount = request.CoilsAmount;
        await _context.SaveChangesAsync(cancellationToken);

        return new ProviderInventoryResponse
        {
            Id = inventory.Id,
            ProviderId = inventory.ProviderId,
            CoilsCount = inventory.CoilsCount,
        };
    }
}

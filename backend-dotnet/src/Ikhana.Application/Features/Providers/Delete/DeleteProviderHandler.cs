using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Providers;

public class DeleteProviderHandler : IRequestHandler<DeleteProviderCommand, bool>
{
    private readonly IAppDbContext _context;

    public DeleteProviderHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<bool> Handle(DeleteProviderCommand request, CancellationToken cancellationToken)
    {
        var provider = await _context.Providers
            .FirstOrDefaultAsync(p => p.Id == request.Id && p.DeletedAt == null, cancellationToken);

        if (provider == null)
            return false;

        _context.Providers.Remove(provider);
        await _context.SaveChangesAsync(cancellationToken);

        return true;
    }
}

using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.TaxStatuses;

public class DeleteTaxStatusHandler : IRequestHandler<DeleteTaxStatusCommand, bool>
{
    private readonly IAppDbContext _context;

    public DeleteTaxStatusHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<bool> Handle(DeleteTaxStatusCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.TaxStatuses
            .FirstOrDefaultAsync(t => t.Id == request.Id && t.DeletedAt == null, cancellationToken);

        if (entity == null)
            return false;

        _context.TaxStatuses.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);

        return true;
    }
}

using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Agreements;

public class DeleteAgreementHandler : IRequestHandler<DeleteAgreementCommand, bool>
{
    private readonly IAppDbContext _context;

    public DeleteAgreementHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<bool> Handle(DeleteAgreementCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.Agreements
            .FirstOrDefaultAsync(a => a.Id == request.Id && a.DeletedAt == null, cancellationToken);

        if (entity == null)
            return false;

        _context.Agreements.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);

        return true;
    }
}

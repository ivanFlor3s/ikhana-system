using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Brokers;

public class DeleteBrokerHandler : IRequestHandler<DeleteBrokerCommand, bool>
{
    private readonly IAppDbContext _context;

    public DeleteBrokerHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<bool> Handle(DeleteBrokerCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.Brokers
            .FirstOrDefaultAsync(b => b.Id == request.Id && b.DeletedAt == null, cancellationToken);

        if (entity == null)
            return false;

        _context.Brokers.Remove(entity);
        await _context.SaveChangesAsync(cancellationToken);

        return true;
    }
}

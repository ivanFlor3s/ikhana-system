using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Brokers;

public class UpdateBrokerHandler : IRequestHandler<UpdateBrokerCommand, BrokerResponse?>
{
    private readonly IAppDbContext _context;

    public UpdateBrokerHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<BrokerResponse?> Handle(UpdateBrokerCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.Brokers
            .FirstOrDefaultAsync(b => b.Id == request.Id && b.DeletedAt == null, cancellationToken);

        if (entity == null)
            return null;

        if (request.FirstName != null)
            entity.FirstName = request.FirstName;
        if (request.LastName != null)
            entity.LastName = request.LastName;
        if (request.Email != null)
            entity.Email = request.Email;
        if (request.Phone != null)
            entity.Phone = request.Phone;

        await _context.SaveChangesAsync(cancellationToken);

        return new BrokerResponse
        {
            Id = entity.Id,
            FirstName = entity.FirstName,
            LastName = entity.LastName,
            Email = entity.Email,
            Phone = entity.Phone,
            CreatedAt = entity.CreatedAt,
            UpdatedAt = entity.UpdatedAt,
            DeletedAt = entity.DeletedAt
        };
    }
}

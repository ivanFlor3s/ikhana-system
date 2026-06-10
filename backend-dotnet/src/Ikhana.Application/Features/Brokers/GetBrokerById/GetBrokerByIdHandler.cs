using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Brokers;

public class GetBrokerByIdHandler : IRequestHandler<GetBrokerByIdQuery, BrokerResponse?>
{
    private readonly IAppDbContext _context;

    public GetBrokerByIdHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<BrokerResponse?> Handle(GetBrokerByIdQuery request, CancellationToken cancellationToken)
    {
        return await _context.Brokers
            .Where(b => b.Id == request.Id && b.DeletedAt == null)
            .Select(b => new BrokerResponse
            {
                Id = b.Id,
                FirstName = b.FirstName,
                LastName = b.LastName,
                Email = b.Email,
                Phone = b.Phone,
                CreatedAt = b.CreatedAt,
                UpdatedAt = b.UpdatedAt,
                DeletedAt = b.DeletedAt
            })
            .FirstOrDefaultAsync(cancellationToken);
    }
}

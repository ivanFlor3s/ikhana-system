using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using Ikhana.Domain.Entities;
using MediatR;

namespace Ikhana.Application.Features.Brokers;

public class CreateBrokerHandler : IRequestHandler<CreateBrokerCommand, BrokerResponse>
{
    private readonly IAppDbContext _context;

    public CreateBrokerHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<BrokerResponse> Handle(CreateBrokerCommand request, CancellationToken cancellationToken)
    {
        var entity = new Broker
        {
            FirstName = request.FirstName,
            LastName = request.LastName,
            Email = request.Email,
            Phone = request.Phone
        };

        _context.Brokers.Add(entity);
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

using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using Ikhana.Domain.Entities;
using MediatR;

namespace Ikhana.Application.Features.Providers;

public class CreateProviderBrokerHandler : IRequestHandler<CreateProviderBrokerCommand, BrokerResponse>
{
    private readonly IAppDbContext _context;

    public CreateProviderBrokerHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<BrokerResponse> Handle(CreateProviderBrokerCommand request, CancellationToken cancellationToken)
    {
        var entity = new Broker
        {
            FirstName = request.FirstName,
            LastName = request.LastName,
            Email = request.Email,
            Phone = request.Phone,
            ProviderId = request.ProviderId,
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
            DeletedAt = entity.DeletedAt,
        };
    }
}

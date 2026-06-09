using AutoMapper;
using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using Ikhana.Domain.Entities;
using MediatR;

namespace Ikhana.Application.Features.Brokers;

public class CreateBrokerHandler : IRequestHandler<CreateBrokerCommand, BrokerResponse>
{
    private readonly IAppDbContext _context;
    private readonly IMapper _mapper;

    public CreateBrokerHandler(IAppDbContext context, IMapper mapper)
    {
        _context = context;
        _mapper = mapper;
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

        return _mapper.Map<BrokerResponse>(entity);
    }
}

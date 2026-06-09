using AutoMapper;
using AutoMapper.QueryableExtensions;
using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Brokers;

public class GetBrokerByIdHandler : IRequestHandler<GetBrokerByIdQuery, BrokerResponse?>
{
    private readonly IAppDbContext _context;
    private readonly IMapper _mapper;

    public GetBrokerByIdHandler(IAppDbContext context, IMapper mapper)
    {
        _context = context;
        _mapper = mapper;
    }

    public async Task<BrokerResponse?> Handle(GetBrokerByIdQuery request, CancellationToken cancellationToken)
    {
        return await _context.Brokers
            .Where(b => b.Id == request.Id && b.DeletedAt == null)
            .ProjectTo<BrokerResponse>(_mapper.ConfigurationProvider)
            .FirstOrDefaultAsync(cancellationToken);
    }
}

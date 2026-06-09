using AutoMapper;
using AutoMapper.QueryableExtensions;
using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.TaxStatuses;

public class GetTaxStatusByIdHandler : IRequestHandler<GetTaxStatusByIdQuery, TaxStatusDetailResponse?>
{
    private readonly IAppDbContext _context;
    private readonly IMapper _mapper;

    public GetTaxStatusByIdHandler(IAppDbContext context, IMapper mapper)
    {
        _context = context;
        _mapper = mapper;
    }

    public async Task<TaxStatusDetailResponse?> Handle(GetTaxStatusByIdQuery request, CancellationToken cancellationToken)
    {
        return await _context.TaxStatuses
            .Where(t => t.Id == request.Id && t.DeletedAt == null)
            .ProjectTo<TaxStatusDetailResponse>(_mapper.ConfigurationProvider)
            .FirstOrDefaultAsync(cancellationToken);
    }
}

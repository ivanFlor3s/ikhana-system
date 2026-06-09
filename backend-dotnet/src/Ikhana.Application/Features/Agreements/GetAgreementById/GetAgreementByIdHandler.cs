using AutoMapper;
using AutoMapper.QueryableExtensions;
using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Agreements;

public class GetAgreementByIdHandler : IRequestHandler<GetAgreementByIdQuery, AgreementDetailResponse?>
{
    private readonly IAppDbContext _context;
    private readonly IMapper _mapper;

    public GetAgreementByIdHandler(IAppDbContext context, IMapper mapper)
    {
        _context = context;
        _mapper = mapper;
    }

    public async Task<AgreementDetailResponse?> Handle(GetAgreementByIdQuery request, CancellationToken cancellationToken)
    {
        return await _context.Agreements
            .Where(a => a.Id == request.Id && a.DeletedAt == null)
            .ProjectTo<AgreementDetailResponse>(_mapper.ConfigurationProvider)
            .FirstOrDefaultAsync(cancellationToken);
    }
}

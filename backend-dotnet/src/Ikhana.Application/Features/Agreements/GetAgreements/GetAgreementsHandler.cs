using AutoMapper;
using AutoMapper.QueryableExtensions;
using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Agreements;

public class GetAgreementsHandler : IRequestHandler<GetAgreementsQuery, PaginatedList<AgreementResponse>>
{
    private readonly IAppDbContext _context;
    private readonly IMapper _mapper;

    public GetAgreementsHandler(IAppDbContext context, IMapper mapper)
    {
        _context = context;
        _mapper = mapper;
    }

    public async Task<PaginatedList<AgreementResponse>> Handle(GetAgreementsQuery request, CancellationToken cancellationToken)
    {
        var query = _context.Agreements.Where(a => a.DeletedAt == null);

        query = request.SortBy.ToLower() switch
        {
            "created_at" => request.SortOrder.ToLower() == "desc"
                ? query.OrderByDescending(a => a.CreatedAt)
                : query.OrderBy(a => a.CreatedAt),
            _ => request.SortOrder.ToLower() == "desc"
                ? query.OrderByDescending(a => a.Name)
                : query.OrderBy(a => a.Name)
        };

        var totalCount = await query.CountAsync(cancellationToken);

        var items = await query
            .Skip((request.Page - 1) * request.PageSize)
            .Take(request.PageSize)
            .ProjectTo<AgreementResponse>(_mapper.ConfigurationProvider)
            .ToListAsync(cancellationToken);

        return new PaginatedList<AgreementResponse>
        {
            Items = items,
            Page = request.Page,
            PageSize = request.PageSize,
            TotalCount = totalCount
        };
    }
}

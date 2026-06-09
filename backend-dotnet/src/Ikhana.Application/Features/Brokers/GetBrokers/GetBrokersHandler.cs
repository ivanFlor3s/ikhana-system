using AutoMapper;
using AutoMapper.QueryableExtensions;
using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Brokers;

public class GetBrokersHandler : IRequestHandler<GetBrokersQuery, PaginatedList<BrokerResponse>>
{
    private readonly IAppDbContext _context;
    private readonly IMapper _mapper;

    public GetBrokersHandler(IAppDbContext context, IMapper mapper)
    {
        _context = context;
        _mapper = mapper;
    }

    public async Task<PaginatedList<BrokerResponse>> Handle(GetBrokersQuery request, CancellationToken cancellationToken)
    {
        var query = _context.Brokers.Where(b => b.DeletedAt == null);

        query = request.SortBy.ToLower() switch
        {
            "first_name" => request.SortOrder.ToLower() == "desc"
                ? query.OrderByDescending(b => b.FirstName).ThenBy(b => b.LastName)
                : query.OrderBy(b => b.FirstName).ThenBy(b => b.LastName),
            "email" => request.SortOrder.ToLower() == "desc"
                ? query.OrderByDescending(b => b.Email)
                : query.OrderBy(b => b.Email),
            "created_at" => request.SortOrder.ToLower() == "desc"
                ? query.OrderByDescending(b => b.CreatedAt)
                : query.OrderBy(b => b.CreatedAt),
            _ => request.SortOrder.ToLower() == "desc"
                ? query.OrderByDescending(b => b.LastName).ThenBy(b => b.FirstName)
                : query.OrderBy(b => b.LastName).ThenBy(b => b.FirstName)
        };

        var totalCount = await query.CountAsync(cancellationToken);

        var items = await query
            .Skip((request.Page - 1) * request.PageSize)
            .Take(request.PageSize)
            .ProjectTo<BrokerResponse>(_mapper.ConfigurationProvider)
            .ToListAsync(cancellationToken);

        return new PaginatedList<BrokerResponse>
        {
            Items = items,
            Page = request.Page,
            PageSize = request.PageSize,
            TotalCount = totalCount
        };
    }
}

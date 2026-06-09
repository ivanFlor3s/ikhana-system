using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.TaxStatuses;

public class GetTaxStatusByIdHandler : IRequestHandler<GetTaxStatusByIdQuery, TaxStatusDetailResponse?>
{
    private readonly IAppDbContext _context;

    public GetTaxStatusByIdHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<TaxStatusDetailResponse?> Handle(GetTaxStatusByIdQuery request, CancellationToken cancellationToken)
    {
        return await _context.TaxStatuses
            .Where(t => t.Id == request.Id && t.DeletedAt == null)
            .Select(t => new TaxStatusDetailResponse
            {
                Id = t.Id,
                Code = t.Code,
                Name = t.Name,
                Description = t.Description,
                IsActive = t.IsActive,
                CreatedAt = t.CreatedAt,
                UpdatedAt = t.UpdatedAt,
                DeletedAt = t.DeletedAt,
                ProvidersCount = t.Providers.Count
            })
            .FirstOrDefaultAsync(cancellationToken);
    }
}

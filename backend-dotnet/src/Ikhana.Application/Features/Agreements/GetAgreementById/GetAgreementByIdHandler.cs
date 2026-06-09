using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Agreements;

public class GetAgreementByIdHandler : IRequestHandler<GetAgreementByIdQuery, AgreementDetailResponse?>
{
    private readonly IAppDbContext _context;

    public GetAgreementByIdHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<AgreementDetailResponse?> Handle(GetAgreementByIdQuery request, CancellationToken cancellationToken)
    {
        return await _context.Agreements
            .Where(a => a.Id == request.Id && a.DeletedAt == null)
            .Select(a => new AgreementDetailResponse
            {
                Id = a.Id,
                Code = a.Code,
                Name = a.Name,
                Description = a.Description,
                IsActive = a.IsActive,
                CreatedAt = a.CreatedAt,
                UpdatedAt = a.UpdatedAt,
                DeletedAt = a.DeletedAt,
                ProvidersCount = a.Providers.Count
            })
            .FirstOrDefaultAsync(cancellationToken);
    }
}

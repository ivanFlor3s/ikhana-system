using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.RawMaterials.GetTypes;

public class GetRawMaterialTypesHandler : IRequestHandler<GetRawMaterialTypesQuery, List<RawMaterialTypeResponse>>
{
    private readonly IAppDbContext _context;

    public GetRawMaterialTypesHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<List<RawMaterialTypeResponse>> Handle(GetRawMaterialTypesQuery request, CancellationToken cancellationToken)
    {
        return await _context.RawMaterialTypes
            .Where(t => t.DeletedAt == null)
            .Select(t => new RawMaterialTypeResponse { Id = t.Id, Name = t.Name })
            .ToListAsync(cancellationToken);
    }
}

using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.RawMaterials.GetDiameters;

public class GetDiametersHandler : IRequestHandler<GetDiametersQuery, List<DiameterResponse>>
{
    private readonly IAppDbContext _context;

    public GetDiametersHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<List<DiameterResponse>> Handle(GetDiametersQuery request, CancellationToken cancellationToken)
    {
        var diameters = await _context.RawMaterialCharacteristics
            .Where(c => c.DeletedAt == null && c.IramOhmMaxResistance != null)
            .Include(c => c.IramOhmMaxResistance)
            .ToListAsync(cancellationToken);

        return diameters.Select(c => new DiameterResponse
        {
            CharacteristicId = c.Id,
            Name = c.Name,
            Description = c.Description,
            DecimalValue = c.DecimalValue,
            Unit = c.Unit,
            MaxResistanceOhmKm = c.IramOhmMaxResistance?.MaxResistanceOhmKm
        }).ToList();
    }
}

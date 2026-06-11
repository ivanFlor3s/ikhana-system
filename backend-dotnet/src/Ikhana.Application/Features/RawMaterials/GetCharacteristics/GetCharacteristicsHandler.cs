using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.RawMaterials.GetCharacteristics;

public class GetCharacteristicsHandler : IRequestHandler<GetCharacteristicsQuery, List<RawMaterialCharacteristicResponse>>
{
    private readonly IAppDbContext _context;

    public GetCharacteristicsHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<List<RawMaterialCharacteristicResponse>> Handle(GetCharacteristicsQuery request, CancellationToken cancellationToken)
    {
        var characteristics = await _context.RawMaterialCharacteristics
            .Where(c => c.RawMaterialTypeId == request.TypeId && c.DeletedAt == null)
            .ToListAsync(cancellationToken);

        return characteristics.Select(c => new RawMaterialCharacteristicResponse
        {
            Id = c.Id,
            Name = c.Name,
            Description = c.Description,
            Unit = c.Unit,
            DecimalValue = c.DecimalValue,
            TextValue = c.TextValue
        }).ToList();
    }
}

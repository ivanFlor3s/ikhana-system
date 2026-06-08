using Ikhana.Application.Common.Interfaces;
using Ikhana.Domain.Entities;
using Ikhana.Infrastructure.Persistence;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Infrastructure.Data.Seeders;

public class RawMaterialSeeder : IDataSeeder
{
    private readonly AppDbContext _context;

    public int Order => 5;

    public RawMaterialSeeder(AppDbContext context)
    {
        _context = context;
    }

    public async Task SeedAsync(CancellationToken cancellationToken = default)
    {
        if (await _context.Set<RawMaterialType>().AnyAsync(cancellationToken))
            return;

        var types = new[] { "Cobre", "PVC", "Master", "Varios" };
        foreach (var name in types)
        {
            _context.Set<RawMaterialType>().Add(new RawMaterialType { Name = name });
        }

        await _context.SaveChangesAsync(cancellationToken);

        var copperType = await _context.Set<RawMaterialType>()
            .FirstAsync(t => t.Name == "Cobre", cancellationToken);

        var characteristics = new (string Name, decimal Diameter, decimal MaxResistance)[]
        {
            ("0.30", 0.30m, 256.00m),
            ("0.35", 0.35m, 188.10m),
            ("0.38", 0.38m, 159.50m),
            ("0.40", 0.40m, 144.10m),
            ("0.50", 0.50m, 92.40m),
            ("0.60", 0.60m, 63.90m),
            ("0.67", 0.67m, 51.60m),
            ("0.85", 0.85m, 32.00m),
            ("1.05", 1.05m, 21.00m),
            ("1.13", 1.13m, 18.20m),
            ("1.35", 1.35m, 12.41m),
        };

        foreach (var (name, diameter, maxResistance) in characteristics)
        {
            var characteristic = new RawMaterialCharacteristic
            {
                RawMaterialTypeId = copperType.Id,
                Name = name,
                DecimalValue = diameter,
                Unit = "mm",
            };

            _context.Set<RawMaterialCharacteristic>().Add(characteristic);
            await _context.SaveChangesAsync(cancellationToken);

            _context.Set<IramOhmMaxResistance>().Add(new IramOhmMaxResistance
            {
                RawMaterialCharacteristicId = characteristic.Id,
                MaxResistanceOhmKm = maxResistance,
            });
        }

        await _context.SaveChangesAsync(cancellationToken);
    }
}

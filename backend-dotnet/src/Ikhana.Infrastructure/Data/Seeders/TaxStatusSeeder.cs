using Ikhana.Application.Common.Interfaces;
using Ikhana.Domain.Entities;
using Ikhana.Infrastructure.Persistence;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Infrastructure.Data.Seeders;

public class TaxStatusSeeder : IDataSeeder
{
    private readonly AppDbContext _context;

    public int Order => 3;

    public TaxStatusSeeder(AppDbContext context)
    {
        _context = context;
    }

    public async Task SeedAsync(CancellationToken cancellationToken = default)
    {
        if (await _context.Set<TaxStatus>().AnyAsync(cancellationToken))
            return;

        var statuses = new (string Code, string Name)[]
        {
            ("1", "Responsable Inscripto"),
            ("2", "Responsable no Inscripto"),
            ("3", "No Responsable"),
            ("4", "Sujeto Exento"),
            ("5", "Consumidor Final"),
            ("6", "Responsable Monotributo"),
            ("7", "Sujeto no Categorizado"),
            ("8", "Proveedor del Exterior"),
            ("9", "Cliente del Exterior"),
            ("10", "Liberado - Ley N° 19.640"),
            ("11", "Agente de Percepción"),
            ("12", "Pequeño Contribuyente Eventual"),
            ("13", "Monotributista Social"),
            ("14", "Pequeño Contribuyente Eventual Social"),
        };

        foreach (var (code, name) in statuses)
        {
            _context.Set<TaxStatus>().Add(new TaxStatus
            {
                Code = code,
                Name = name,
                IsActive = true,
            });
        }

        await _context.SaveChangesAsync(cancellationToken);
    }
}

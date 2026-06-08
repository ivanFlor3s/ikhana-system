using Ikhana.Application.Common.Interfaces;
using Ikhana.Domain.Entities;
using Ikhana.Infrastructure.Persistence;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Infrastructure.Data.Seeders;

public class AgreementSeeder : IDataSeeder
{
    private readonly AppDbContext _context;

    public int Order => 3;

    public AgreementSeeder(AppDbContext context)
    {
        _context = context;
    }

    public async Task SeedAsync(CancellationToken cancellationToken = default)
    {
        if (await _context.Set<Agreement>().AnyAsync(cancellationToken))
            return;

        _context.Set<Agreement>().Add(new Agreement
        {
            Code = "convenio_multilateral",
            Name = "Convenio Multilateral",
            Description = "Convenio Multilateral del Impuesto sobre los Ingresos Brutos",
            IsActive = true,
        });

        await _context.SaveChangesAsync(cancellationToken);
    }
}

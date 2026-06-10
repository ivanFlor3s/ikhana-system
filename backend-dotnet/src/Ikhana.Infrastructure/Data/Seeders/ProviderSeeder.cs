using Ikhana.Application.Common.Interfaces;
using Ikhana.Domain.Entities;
using Ikhana.Infrastructure.Persistence;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Infrastructure.Data.Seeders;

public class ProviderSeeder : IDataSeeder
{
    private readonly AppDbContext _context;

    public int Order => 6;

    public ProviderSeeder(AppDbContext context)
    {
        _context = context;
    }

    public async Task SeedAsync(CancellationToken cancellationToken = default)
    {
        if (await _context.Set<Provider>().AnyAsync(cancellationToken))
            return;

        var taxStatus = await _context.Set<TaxStatus>()
            .FirstOrDefaultAsync(t => t.Code == "1", cancellationToken);

        var agreement = await _context.Set<Agreement>()
            .FirstOrDefaultAsync(a => a.Code == "convenio_multilateral", cancellationToken);

        var providers = new (string FantasyName, string BusinessName, string Cuit, string? Phone, string? Email)[]
        {
            ("Distribuidora Norte S.A.", "Distribuidora Norte Sociedad Anonima", "30-12345678-9", "+54 11 4123-4567", "contacto@distribuidoranorte.com"),
            ("Tech Solutions Argentina S.R.L.", "Tech Solutions Argentina Sociedad de Responsabilidad Limitada", "30-98765432-1", "+54 11 5234-5678", "info@techsolutions.com.ar"),
            ("Alimentos del Sur S.A.", "Alimentos del Sur Sociedad Anonima", "30-11223344-5", "+54 11 6345-6789", "ventas@alimentosdelsur.com.ar"),
        };

        foreach (var (fantasyName, businessName, cuit, phone, email) in providers)
        {
            _context.Set<Provider>().Add(new Provider
            {
                FantasyName = fantasyName,
                BusinessName = businessName,
                Cuit = cuit,
                Phone = phone,
                Email = email,
                TaxStatusId = taxStatus?.Id,
                AgreementId = agreement?.Id,
            });
        }

        await _context.SaveChangesAsync(cancellationToken);
    }
}

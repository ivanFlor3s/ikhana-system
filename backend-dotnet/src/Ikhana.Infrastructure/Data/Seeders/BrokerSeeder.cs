using Ikhana.Application.Common.Interfaces;
using Ikhana.Domain.Entities;
using Ikhana.Infrastructure.Persistence;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Infrastructure.Data.Seeders;

public class BrokerSeeder : IDataSeeder
{
    private readonly AppDbContext _context;

    public int Order => 4;

    public BrokerSeeder(AppDbContext context)
    {
        _context = context;
    }

    public async Task SeedAsync(CancellationToken cancellationToken = default)
    {
        if (await _context.Set<Broker>().AnyAsync(cancellationToken))
            return;

        var brokers = new (string FirstName, string LastName, string Email, string Phone)[]
        {
            ("Juan", "Perez", "juan.perez@corredores.com", "+54 11 4444-5555"),
            ("Maria", "Gonzalez", "maria.gonzalez@brokers.com", "+54 11 5555-6666"),
            ("Carlos", "Rodriguez", "carlos.rodriguez@contactos.com", "+54 11 6666-7777"),
            ("Ana", "Martinez", "ana.martinez@agentes.com", "+54 11 7777-8888"),
            ("Roberto", "Lopez", "roberto.lopez@intermediarios.com", "+54 11 8888-9999"),
        };

        foreach (var (firstName, lastName, email, phone) in brokers)
        {
            _context.Set<Broker>().Add(new Broker
            {
                FirstName = firstName,
                LastName = lastName,
                Email = email,
                Phone = phone,
            });
        }

        await _context.SaveChangesAsync(cancellationToken);
    }
}

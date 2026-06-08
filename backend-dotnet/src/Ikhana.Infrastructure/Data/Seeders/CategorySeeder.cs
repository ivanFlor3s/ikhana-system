using Ikhana.Application.Common.Interfaces;
using Ikhana.Domain.Entities;
using Ikhana.Infrastructure.Persistence;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Infrastructure.Data.Seeders;

public class CategorySeeder : IDataSeeder
{
    private readonly AppDbContext _context;

    public int Order => 3;

    public CategorySeeder(AppDbContext context)
    {
        _context = context;
    }

    public async Task SeedAsync(CancellationToken cancellationToken = default)
    {
        if (await _context.Set<Category>().AnyAsync(cancellationToken))
            return;

        var categories = new (string Name, string Description)[]
        {
            ("Cobre", "Filamento de cobre"),
            ("Cuerda", "Filamentos de cobre ya tejidos"),
            ("PVC", "Policlorulo de vinilo"),
            ("Master", "Master"),
        };

        foreach (var (name, description) in categories)
        {
            _context.Set<Category>().Add(new Category
            {
                Name = name,
                Description = description,
            });
        }

        await _context.SaveChangesAsync(cancellationToken);
    }
}

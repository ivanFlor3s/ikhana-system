using Ikhana.Application.Common.Interfaces;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Identity;

namespace Ikhana.Infrastructure.Data.Seeders;

public class RoleSeeder : IDataSeeder
{
    private readonly RoleManager<ApplicationRole> _roleManager;

    public int Order => 1;

    public RoleSeeder(RoleManager<ApplicationRole> roleManager)
    {
        _roleManager = roleManager;
    }

    public async Task SeedAsync(CancellationToken cancellationToken = default)
    {
        var roles = new (string Name, string Description)[]
        {
            ("Admin", "Administrador del sistema con acceso completo"),
            ("Operador", "Usuario con permisos de creacion y edicion justos para cumplir sus tareas"),
            ("Administracion", "Usuario con permisos de creacion y edicion, mas involucrado en operactiones de administracion"),
            ("Consultor", "Consultor con acceso de solo lectura"),
        };

        foreach (var (name, description) in roles)
        {
            if (await _roleManager.FindByNameAsync(name) is not null)
                continue;

            var role = new ApplicationRole
            {
                Name = name,
                Description = description,
                ConcurrencyStamp = Guid.NewGuid().ToString(),
            };

            await _roleManager.CreateAsync(role);
        }
    }
}

using Ikhana.Application.Common.Interfaces;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Identity;

namespace Ikhana.Infrastructure.Data.Seeders;

public class UserSeeder : IDataSeeder
{
    private readonly UserManager<ApplicationUser> _userManager;

    public int Order => 2;

    public UserSeeder(UserManager<ApplicationUser> userManager)
    {
        _userManager = userManager;
    }

    public async Task SeedAsync(CancellationToken cancellationToken = default)
    {
        var users = new (string Name, string Email, string Password, string Role)[]
        {
            ("Administrador", "admin@ikhana.com", "admin123", "Admin"),
            ("Juan Empleado", "empleado@ikhana.com", "empleado123", "Operador"),
            ("Maria Consultora", "consultor@ikhana.com", "consultor123", "Consultor"),
        };

        foreach (var (name, email, password, role) in users)
        {
            if (await _userManager.FindByEmailAsync(email) is not null)
                continue;

            var user = new ApplicationUser
            {
                UserName = email,
                Email = email,
                Name = name,
                EmailConfirmed = true,
            };

            var result = await _userManager.CreateAsync(user, password);

            if (!result.Succeeded)
                throw new InvalidOperationException(
                    $"Failed to create user {email}: {string.Join(", ", result.Errors.Select(e => e.Description))}");

            await _userManager.AddToRoleAsync(user, role);
        }
    }
}

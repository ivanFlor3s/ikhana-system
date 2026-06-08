using Ikhana.Application.Common.Interfaces;
using Ikhana.Domain.Entities;
using Ikhana.Infrastructure.Data.Seeders;
using Ikhana.Infrastructure.Persistence;
using Ikhana.Infrastructure.Services;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;

namespace Ikhana.Infrastructure;

public static class DependencyInjection
{
    public static IServiceCollection AddInfrastructure(
        this IServiceCollection services,
        IConfiguration configuration)
    {
        var connectionString = configuration.GetConnectionString("DefaultConnection");

        services.AddDbContext<AppDbContext>(options =>
            options.UseMySql(
                connectionString,
                ServerVersion.AutoDetect(connectionString),
                mysqlOptions => mysqlOptions
                    .MigrationsAssembly(typeof(AppDbContext).Assembly.FullName)));

        services.AddScoped<IAppDbContext>(provider => provider.GetRequiredService<AppDbContext>());

        services.AddIdentityCore<ApplicationUser>(options =>
        {
            options.Password.RequireDigit = false;
            options.Password.RequireLowercase = false;
            options.Password.RequireNonAlphanumeric = false;
            options.Password.RequireUppercase = false;
            options.Password.RequiredLength = 6;
        })
        .AddRoles<ApplicationRole>()
        .AddEntityFrameworkStores<AppDbContext>()
        .AddDefaultTokenProviders();

        services.AddScoped<IAuthService, AuthService>();
        services.AddScoped<ITokenService, TokenService>();

        services.AddScoped<IDataSeeder, RoleSeeder>();
        services.AddScoped<IDataSeeder, UserSeeder>();
        services.AddScoped<IDataSeeder, TaxStatusSeeder>();
        services.AddScoped<IDataSeeder, AgreementSeeder>();
        services.AddScoped<IDataSeeder, CategorySeeder>();
        services.AddScoped<IDataSeeder, BrokerSeeder>();
        services.AddScoped<IDataSeeder, RawMaterialSeeder>();
        services.AddScoped<IDataSeeder, ProviderSeeder>();

        return services;
    }
}

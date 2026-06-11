using System.Net;
using System.Net.Http.Headers;
using System.Net.Http.Json;
using FluentAssertions;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.DependencyInjection;

namespace Ikhana.Api.Tests.Integration;

public class RawMaterialTests : IClassFixture<TestWebApplicationFactory>, IAsyncLifetime
{
    private readonly HttpClient _client;
    private readonly IServiceProvider _services;

    public RawMaterialTests(TestWebApplicationFactory factory)
    {
        _client = factory.CreateClient();
        _services = factory.Services;
    }

    public async Task InitializeAsync()
    {
        using var scope = _services.CreateAsyncScope();
        var roleManager = scope.ServiceProvider.GetRequiredService<RoleManager<ApplicationRole>>();
        var userManager = scope.ServiceProvider.GetRequiredService<UserManager<ApplicationUser>>();

        if (!await roleManager.RoleExistsAsync("Admin"))
            await roleManager.CreateAsync(new ApplicationRole { Name = "Admin", Description = "Administrator" });

        if (await userManager.FindByEmailAsync("admin@ikhana.com") == null)
        {
            var user = new ApplicationUser { UserName = "admin@ikhana.com", Email = "admin@ikhana.com", Name = "Administrador", EmailConfirmed = true };
            await userManager.CreateAsync(user, "admin123");
            await userManager.AddToRoleAsync(user, "Admin");
        }
    }

    public Task DisposeAsync() => Task.CompletedTask;

    private async Task AuthenticateAsync()
    {
        var login = await _client.PostAsJsonAsync("/api/auth/login", new { email = "admin@ikhana.com", password = "admin123" });
        var content = await login.Content.ReadFromJsonAsync<LoginWrap>();
        _client.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", content!.data.token);
    }

    [Fact]
    public async Task IndexTypes_returns_all_types()
    {
        await AuthenticateAsync();
        var response = await _client.GetAsync("/api/raw-materials/types");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<List<TypeData>>>();
        content!.success.Should().BeTrue();
        content.data.Should().NotBeEmpty();
        content.data.Should().Contain(t => t.name == "Cobre");
    }

    [Fact]
    public async Task IndexCharacteristics_returns_for_valid_type()
    {
        await AuthenticateAsync();
        var typesResp = await _client.GetAsync("/api/raw-materials/types");
        var types = await typesResp.Content.ReadFromJsonAsync<DataWrap<List<TypeData>>>();
        var typeId = types!.data.First().id;

        var response = await _client.GetAsync($"/api/raw-materials/types/{typeId}/characteristics");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<List<CharData>>>();
        content!.success.Should().BeTrue();
        content.data.Should().NotBeEmpty();
    }

    [Fact]
    public async Task ValidateResistance_valid_diameter_passes()
    {
        await AuthenticateAsync();
        var typesResp = await _client.GetAsync("/api/raw-materials/types");
        var types = await typesResp.Content.ReadFromJsonAsync<DataWrap<List<TypeData>>>();
        var typeId = types!.data.First().id;

        var charsResp = await _client.GetAsync($"/api/raw-materials/types/{typeId}/characteristics");
        var chars = await charsResp.Content.ReadFromJsonAsync<DataWrap<List<CharData>>>();
        var diameter = chars!.data.FirstOrDefault(c => c.name == "Diámetro");
        if (diameter == null) return;

        var response = await _client.PostAsJsonAsync("/api/raw-materials/validate-resistance", new
        {
            rawMaterialCharacteristicId = diameter.id,
            resistanceOhmKm = 10.0m
        });

        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<ValidateData>>();
        content!.data.hasRule.Should().BeTrue();
        content.data.valid.Should().BeTrue();
    }

    [Fact]
    public async Task GetDiametersAndIram_returns_diameters_with_resistance()
    {
        await AuthenticateAsync();
        var response = await _client.GetAsync("/api/raw-materials/diameters-and-iram");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<List<DiameterData>>>();
        content!.success.Should().BeTrue();
        content.data.Should().NotBeEmpty();
        content.data.Should().Contain(d => d.maxResistanceOhmKm.HasValue);
    }

    private record TypeData(long id, string name);
    private record CharData(long id, string name, string description, string? unit, decimal? decimalValue, string? textValue);
    private record ValidateData(bool valid, decimal? maxAllowed, decimal measured, bool hasRule);
    private record DiameterData(long characteristicId, string name, string description, decimal? decimalValue, string? unit, decimal? maxResistanceOhmKm);
    private record DataWrap<T>(bool success, T data, string message);
    private record LoginWrap(bool success, LoginData data, string message);
    private record LoginData(long userId, string email, string name, string? role, string token);
}

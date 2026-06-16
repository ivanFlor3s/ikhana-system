using System.Net;
using System.Net.Http.Headers;
using System.Net.Http.Json;
using FluentAssertions;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.DependencyInjection;

namespace Ikhana.Api.Tests.Integration;

public class ProviderInventoryTests : IClassFixture<TestWebApplicationFactory>, IAsyncLifetime
{
    private readonly HttpClient _client;
    private readonly IServiceProvider _services;

    public ProviderInventoryTests(TestWebApplicationFactory factory)
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

    private async Task<long> CreateTestProvider()
    {
        var uniqueCuit = "40-" + Guid.NewGuid().ToString("N")[..10] + "-9";
        var response = await _client.PostAsJsonAsync("/api/providers", new
        {
            fantasyName = "Test Inventory Provider",
            businessName = "Test Inventory Provider S.A.",
            cuit = uniqueCuit
        });
        var content = await response.Content.ReadFromJsonAsync<DataWrap<ProviderDetail>>();
        return content!.data.id;
    }

    [Fact]
    public async Task Show_auto_creates_inventory_with_zero()
    {
        await AuthenticateAsync();
        var providerId = await CreateTestProvider();

        var response = await _client.GetAsync($"/api/providers/{providerId}/inventory");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<InventoryData>>();
        content!.success.Should().BeTrue();
        content.data.providerId.Should().Be(providerId);
        content.data.coilsCount.Should().Be(0);
    }

    [Fact]
    public async Task Show_returns_existing_inventory()
    {
        await AuthenticateAsync();
        var providerId = await CreateTestProvider();

        await _client.GetAsync($"/api/providers/{providerId}/inventory");

        var response = await _client.GetAsync($"/api/providers/{providerId}/inventory");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<InventoryData>>();
        content!.data.providerId.Should().Be(providerId);
    }

    [Fact]
    public async Task Create_creates_inventory_and_returns_201()
    {
        await AuthenticateAsync();
        var providerId = await CreateTestProvider();

        var command = new { providerId = providerId, coilsAmount = 10 };
        var response = await _client.PostAsJsonAsync($"/api/providers/{providerId}/inventory", command);
        response.StatusCode.Should().Be(HttpStatusCode.Created);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<InventoryData>>();
        content!.data.coilsCount.Should().Be(10);
    }

    [Fact]
    public async Task Create_returns_409_when_inventory_exists()
    {
        await AuthenticateAsync();
        var providerId = await CreateTestProvider();

        var command = new { providerId = providerId, coilsAmount = 10 };
        await _client.PostAsJsonAsync($"/api/providers/{providerId}/inventory", command);

        var response = await _client.PostAsJsonAsync($"/api/providers/{providerId}/inventory", command);
        response.StatusCode.Should().Be(HttpStatusCode.Conflict);
    }

    [Fact]
    public async Task Update_updates_coils_count()
    {
        await AuthenticateAsync();
        var providerId = await CreateTestProvider();

        var createCmd = new { providerId = providerId, coilsAmount = 10 };
        await _client.PostAsJsonAsync($"/api/providers/{providerId}/inventory", createCmd);

        var updateCmd = new { providerId = providerId, coilsAmount = 42 };
        var response = await _client.PutAsJsonAsync($"/api/providers/{providerId}/inventory", updateCmd);
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<InventoryData>>();
        content!.data.coilsCount.Should().Be(42);
    }

    [Fact]
    public async Task Update_returns_404_when_no_inventory()
    {
        await AuthenticateAsync();
        var providerId = await CreateTestProvider();

        var command = new { providerId = providerId, coilsAmount = 10 };
        var response = await _client.PutAsJsonAsync($"/api/providers/{providerId}/inventory", command);
        response.StatusCode.Should().Be(HttpStatusCode.NotFound);
    }

    [Fact]
    public async Task Unauthenticated_returns_401()
    {
        var response = await _client.GetAsync("/api/providers/1/inventory");
        response.StatusCode.Should().Be(HttpStatusCode.Unauthorized);
    }

    private record InventoryData(long id, long providerId, int coilsCount);
    private record ProviderDetail(long id);
    private record DataWrap<T>(bool success, T data, string message);
    private record LoginWrap(bool success, LoginData data, string message);
    private record LoginData(long userId, string email, string name, string? role, string token);
}

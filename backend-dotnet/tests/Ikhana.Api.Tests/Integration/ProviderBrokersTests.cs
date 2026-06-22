using System.Net;
using System.Net.Http.Headers;
using System.Net.Http.Json;
using FluentAssertions;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.DependencyInjection;

namespace Ikhana.Api.Tests.Integration;

public class ProviderBrokersTests : IClassFixture<TestWebApplicationFactory>, IAsyncLifetime
{
    private readonly HttpClient _client;
    private readonly IServiceProvider _services;

    public ProviderBrokersTests(TestWebApplicationFactory factory)
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
        var uniqueCuit = "50-" + Guid.NewGuid().ToString("N")[..10] + "-9";
        var response = await _client.PostAsJsonAsync("/api/providers", new
        {
            fantasyName = "Broker Test Provider",
            businessName = "Broker Test Provider S.A.",
            cuit = uniqueCuit
        });
        var content = await response.Content.ReadFromJsonAsync<DataWrap<IdData>>();
        return content!.data.id;
    }

    [Fact]
    public async Task Create_creates_broker_linked_to_provider()
    {
        await AuthenticateAsync();
        var providerId = await CreateTestProvider();
        var uniqueEmail = "broker-" + Guid.NewGuid().ToString("N")[..6] + "@test.com";

        var command = new
        {
            providerId,
            firstName = "Juan",
            lastName = "Pérez",
            email = uniqueEmail,
            phone = "+54 11 5555-1234"
        };
        var response = await _client.PostAsJsonAsync($"/api/providers/{providerId}/brokers", command);
        response.StatusCode.Should().Be(HttpStatusCode.Created);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<BrokerData>>();
        content!.data.firstName.Should().Be("Juan");
        content.data.lastName.Should().Be("Pérez");
        content.data.email.Should().Be(uniqueEmail);
    }

    [Fact]
    public async Task Create_returns_422_for_duplicate_email()
    {
        await AuthenticateAsync();
        var providerId = await CreateTestProvider();
        var sharedEmail = "dup-" + Guid.NewGuid().ToString("N")[..6] + "@test.com";

        var command = new
        {
            providerId,
            firstName = "First",
            lastName = "Broker",
            email = sharedEmail,
            phone = "123"
        };
        await _client.PostAsJsonAsync($"/api/providers/{providerId}/brokers", command);

        var otherProviderId = await CreateTestProvider();
        var command2 = new
        {
            providerId = otherProviderId,
            firstName = "Second",
            lastName = "Broker",
            email = sharedEmail,
            phone = "456"
        };
        var response = await _client.PostAsJsonAsync($"/api/providers/{otherProviderId}/brokers", command2);
        response.StatusCode.Should().Be(HttpStatusCode.UnprocessableEntity);
    }

    [Fact]
    public async Task Create_returns_422_for_missing_provider()
    {
        await AuthenticateAsync();

        var command = new
        {
            providerId = 999999,
            firstName = "Ghost",
            lastName = "Broker",
            email = "ghost@test.com"
        };
        var response = await _client.PostAsJsonAsync("/api/providers/999999/brokers", command);
        response.StatusCode.Should().Be(HttpStatusCode.UnprocessableEntity);
    }

    [Fact]
    public async Task Create_unauthenticated_returns_401()
    {
        var response = await _client.PostAsJsonAsync("/api/providers/1/brokers", new { });
        response.StatusCode.Should().Be(HttpStatusCode.Unauthorized);
    }

    private record IdData(long id);
    private record BrokerData(long id, string firstName, string lastName, string email);
    private record DataWrap<T>(bool success, T data, string message);
    private record LoginWrap(bool success, LoginData data, string message);
    private record LoginData(long userId, string email, string name, string? role, string token);
}

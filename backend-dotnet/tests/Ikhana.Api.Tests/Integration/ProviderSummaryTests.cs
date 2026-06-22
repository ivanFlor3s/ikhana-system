using System.Net;
using System.Net.Http.Headers;
using System.Net.Http.Json;
using FluentAssertions;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.DependencyInjection;

namespace Ikhana.Api.Tests.Integration;

public class ProviderSummaryTests : IClassFixture<TestWebApplicationFactory>, IAsyncLifetime
{
    private readonly HttpClient _client;
    private readonly IServiceProvider _services;

    public ProviderSummaryTests(TestWebApplicationFactory factory)
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
    public async Task GetAllProvidersSummary_returns_all_providers()
    {
        await AuthenticateAsync();
        var response = await _client.GetAsync("/api/providers/summary");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<List<ProviderSummaryData>>>();
        content!.success.Should().BeTrue();
        content.data.Should().NotBeEmpty();
    }

    [Fact]
    public async Task GetAllProvidersSummary_sorted_by_fantasy_name()
    {
        await AuthenticateAsync();
        var response = await _client.GetAsync("/api/providers/summary");
        var content = await response.Content.ReadFromJsonAsync<DataWrap<List<ProviderSummaryData>>>();
        var names = content!.data!.Select(p => p.fantasyName.ToLower()).ToList();
        names.Should().BeInAscendingOrder();
    }

    [Fact]
    public async Task GetAllProvidersSummary_unauthenticated_returns_401()
    {
        var response = await _client.GetAsync("/api/providers/summary");
        response.StatusCode.Should().Be(HttpStatusCode.Unauthorized);
    }

    private record ProviderSummaryData(long id, string fantasyName, string businessName);
    private record DataWrap<T>(bool success, T data, string message);
    private record LoginWrap(bool success, LoginData data, string message);
    private record LoginData(long userId, string email, string name, string? role, string token);
}

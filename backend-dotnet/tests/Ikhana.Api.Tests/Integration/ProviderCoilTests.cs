using System.Net;
using System.Net.Http.Headers;
using System.Net.Http.Json;
using FluentAssertions;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.DependencyInjection;

namespace Ikhana.Api.Tests.Integration;

public class ProviderCoilTests : IClassFixture<TestWebApplicationFactory>, IAsyncLifetime
{
    private readonly HttpClient _client;
    private readonly IServiceProvider _services;

    public ProviderCoilTests(TestWebApplicationFactory factory)
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
        var uniqueCuit = "41-" + Guid.NewGuid().ToString("N")[..10] + "-9";
        var response = await _client.PostAsJsonAsync("/api/providers", new
        {
            fantasyName = "Coil Test Provider",
            businessName = "Coil Test Provider S.A.",
            cuit = uniqueCuit
        });
        var content = await response.Content.ReadFromJsonAsync<DataWrap<ProviderDetail>>();
        return content!.data.id;
    }

    [Fact]
    public async Task CoilSummary_returns_paginated_list()
    {
        await AuthenticateAsync();

        var response = await _client.GetAsync("/api/providers/coil-summary?pageSize=2");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<PaginatedWrap<SummaryData>>>();
        content!.success.Should().BeTrue();
        content.data.page.Should().Be(1);
        content.data.pageSize.Should().Be(2);
    }

    [Fact]
    public async Task CoilSummary_search_filters_by_provider_name()
    {
        await AuthenticateAsync();

        var providerId = await CreateTestProvider();
        await _client.PostAsJsonAsync($"/api/providers/{providerId}/inventory",
            new { providerId = providerId, coilsAmount = 5 });

        var response = await _client.GetAsync("/api/providers/coil-summary?search=Coil Test");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<PaginatedWrap<SummaryData>>>();
        content!.data.items.Should().NotBeEmpty();
        content.data.items.Should().AllSatisfy(s =>
            s.providerName.Should().Contain("Coil Test"));
    }

    [Fact]
    public async Task CoilMovements_returns_movements_for_provider()
    {
        await AuthenticateAsync();
        var providerId = await CreateTestProvider();

        var response = await _client.GetAsync($"/api/providers/{providerId}/coil-movements");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<PaginatedWrap<MovementData>>>();
        content!.success.Should().BeTrue();
    }

    [Fact]
    public async Task CoilSummary_unauthenticated_returns_401()
    {
        var response = await _client.GetAsync("/api/providers/coil-summary");
        response.StatusCode.Should().Be(HttpStatusCode.Unauthorized);
    }

    [Fact]
    public async Task CoilMovements_unauthenticated_returns_401()
    {
        var response = await _client.GetAsync("/api/providers/1/coil-movements");
        response.StatusCode.Should().Be(HttpStatusCode.Unauthorized);
    }

    private record SummaryData(long providerId, string providerName, int coilsCount);
    private record MovementData(long id, DateTime? date, int coilsReceived, int coilsReturned);
    private record ProviderDetail(long id);
    private record PaginatedWrap<T>(int page, int pageSize, int totalCount, List<T> items);
    private record DataWrap<T>(bool success, T data, string message);
    private record LoginWrap(bool success, LoginData data, string message);
    private record LoginData(long userId, string email, string name, string? role, string token);
}

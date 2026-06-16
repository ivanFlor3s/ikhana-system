using System.Net;
using System.Net.Http.Headers;
using System.Net.Http.Json;
using FluentAssertions;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.DependencyInjection;

namespace Ikhana.Api.Tests.Integration;

public class ProviderCrudTests : IClassFixture<TestWebApplicationFactory>, IAsyncLifetime
{
    private readonly HttpClient _client;
    private readonly IServiceProvider _services;

    public ProviderCrudTests(TestWebApplicationFactory factory)
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
    public async Task Index_returns_paginated_providers()
    {
        await AuthenticateAsync();
        var response = await _client.GetAsync("/api/providers?pageSize=2");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<PaginatedWrap>>();
        content!.success.Should().BeTrue();
        content.data.items.Should().NotBeEmpty();
        content.data.page.Should().Be(1);
        content.data.pageSize.Should().Be(2);
        content.data.totalCount.Should().BeGreaterThan(0);
    }

    [Fact]
    public async Task Index_search_filters_by_name()
    {
        await AuthenticateAsync();
        var response = await _client.GetAsync("/api/providers?search=Distribuidora");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<PaginatedWrap>>();
        content!.data.items.Should().NotBeEmpty();
        content.data.items.Should().AllSatisfy(p =>
            (p.fantasyName + p.businessName).Should().Contain("Distribuidora"));
    }

    [Fact]
    public async Task Store_creates_provider_and_returns_201()
    {
        await AuthenticateAsync();
        var uniqueCuit = "30-" + Guid.NewGuid().ToString("N")[..10] + "-9";
        var command = new
        {
            fantasyName = "Nuevo Proveedor",
            businessName = "Nuevo Proveedor S.A.",
            cuit = uniqueCuit,
            phone = "+54 11 1111-1111",
            email = "nuevo@proveedor.com"
        };
        var response = await _client.PostAsJsonAsync("/api/providers", command);
        response.StatusCode.Should().Be(HttpStatusCode.Created);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<ProviderDetail>>();
        content!.data.fantasyName.Should().Be("Nuevo Proveedor");
        content.data.id.Should().BeGreaterThan(0);
    }

    [Fact]
    public async Task Store_returns_422_for_duplicate_cuit()
    {
        await AuthenticateAsync();
        var command = new { fantasyName = "Duplicado", cuit = "30-12345678-9" };
        var createResponse = await _client.PostAsJsonAsync("/api/providers", command);
        createResponse.StatusCode.Should().Be(HttpStatusCode.UnprocessableEntity);
    }

    [Fact]
    public async Task Show_returns_provider_by_id()
    {
        await AuthenticateAsync();
        var summaryResp = await _client.GetAsync("/api/providers/summary");
        var summary = await summaryResp.Content.ReadFromJsonAsync<DataWrap<List<SummaryData>>>();
        var id = summary!.data.First().id;

        var response = await _client.GetAsync($"/api/providers/{id}");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<ProviderDetail>>();
        content!.data.id.Should().Be(id);
        content.data.fantasyName.Should().NotBeNullOrEmpty();
    }

    [Fact]
    public async Task Show_returns_404_for_missing_id()
    {
        await AuthenticateAsync();
        var response = await _client.GetAsync("/api/providers/999999");
        response.StatusCode.Should().Be(HttpStatusCode.NotFound);
    }

    [Fact]
    public async Task Update_updates_existing_provider()
    {
        await AuthenticateAsync();
        var summaryResp = await _client.GetAsync("/api/providers/summary");
        var summary = await summaryResp.Content.ReadFromJsonAsync<DataWrap<List<SummaryData>>>();
        var id = summary!.data.First().id;

        var command = new
        {
            id = id,
            fantasyName = "Proveedor Actualizado",
            phone = "+54 11 9999-9999"
        };
        var response = await _client.PutAsJsonAsync($"/api/providers/{id}", command);
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<ProviderDetail>>();
        content!.data.fantasyName.Should().Be("Proveedor Actualizado");
        content.data.phone.Should().Be("+54 11 9999-9999");
    }

    [Fact]
    public async Task Update_returns_404_for_missing_id()
    {
        await AuthenticateAsync();
        var command = new { id = 999999, fantasyName = "Ghost" };
        var response = await _client.PutAsJsonAsync("/api/providers/999999", command);
        response.StatusCode.Should().Be(HttpStatusCode.NotFound);
    }

    [Fact]
    public async Task Destroy_soft_deletes_provider()
    {
        await AuthenticateAsync();
        var uniqueCuit = "31-" + Guid.NewGuid().ToString("N")[..10] + "-9";
        var command = new
        {
            fantasyName = "Para Eliminar",
            businessName = "Para Eliminar S.A.",
            cuit = uniqueCuit
        };
        var createResp = await _client.PostAsJsonAsync("/api/providers", command);
        var created = await createResp.Content.ReadFromJsonAsync<DataWrap<ProviderDetail>>();
        var id = created!.data.id;

        var response = await _client.DeleteAsync($"/api/providers/{id}");
        response.StatusCode.Should().Be(HttpStatusCode.OK);

        var showResp = await _client.GetAsync($"/api/providers/{id}");
        showResp.StatusCode.Should().Be(HttpStatusCode.NotFound);
    }

    [Fact]
    public async Task Destroy_returns_404_for_missing_id()
    {
        await AuthenticateAsync();
        var response = await _client.DeleteAsync("/api/providers/999999");
        response.StatusCode.Should().Be(HttpStatusCode.NotFound);
    }

    [Fact]
    public async Task Unauthenticated_returns_401()
    {
        var response = await _client.GetAsync("/api/providers");
        response.StatusCode.Should().Be(HttpStatusCode.Unauthorized);
    }

    private record SummaryData(long id, string fantasyName, string? cuit);
    private record ProviderListData(long id, string fantasyName, string businessName, string? cuit);
    private record ProviderDetail(long id, string fantasyName, string businessName, string? phone);
    private record PaginatedWrap(int page, int pageSize, int totalCount, List<ProviderListData> items);
    private record DataWrap<T>(bool success, T data, string message);
    private record LoginWrap(bool success, LoginData data, string message);
    private record LoginData(long userId, string email, string name, string? role, string token);
}

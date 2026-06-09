using System.Net;
using System.Net.Http.Headers;
using System.Net.Http.Json;
using FluentAssertions;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.DependencyInjection;

namespace Ikhana.Api.Tests.Integration;

public class AgreementTests : IClassFixture<TestWebApplicationFactory>, IAsyncLifetime
{
    private readonly HttpClient _client;
    private readonly IServiceProvider _services;

    public AgreementTests(TestWebApplicationFactory factory)
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
    public async Task Index_returns_paginated_agreements()
    {
        await AuthenticateAsync();
        var response = await _client.GetAsync("/api/agreements?page=1&page_size=10");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<PaginatedWrap<AgreementData>>();
        content!.success.Should().BeTrue();
        content.data.totalCount.Should().BeGreaterThanOrEqualTo(1);
    }

    [Fact]
    public async Task Show_returns_detail_with_providers_count()
    {
        await AuthenticateAsync();
        var code = Guid.NewGuid().ToString("N")[..10];
        var create = await _client.PostAsJsonAsync("/api/agreements", new { code, name = "ShowTest", isActive = true });
        var created = await create.Content.ReadFromJsonAsync<DataWrap<AgreementData>>();
        var id = created!.data.id;

        var response = await _client.GetAsync($"/api/agreements/{id}");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DetailWrap>();
        content!.data.name.Should().Be("ShowTest");
    }

    [Fact]
    public async Task Store_creates_returns_201()
    {
        await AuthenticateAsync();
        var code = Guid.NewGuid().ToString("N")[..10];
        var response = await _client.PostAsJsonAsync("/api/agreements", new { code, name = $"Test {code}", isActive = true });
        response.StatusCode.Should().Be(HttpStatusCode.Created);
    }

    [Fact]
    public async Task Store_duplicate_code_returns_422()
    {
        await AuthenticateAsync();
        var code = Guid.NewGuid().ToString("N")[..10];
        await _client.PostAsJsonAsync("/api/agreements", new { code, name = "First", isActive = true });
        var response = await _client.PostAsJsonAsync("/api/agreements", new { code, name = "Second", isActive = true });
        response.StatusCode.Should().Be(HttpStatusCode.UnprocessableEntity);
    }

    [Fact]
    public async Task Update_modifies_existing()
    {
        await AuthenticateAsync();
        var code = Guid.NewGuid().ToString("N")[..10];
        var create = await _client.PostAsJsonAsync("/api/agreements", new { code, name = "PreUpdate", isActive = true });
        var created = await create.Content.ReadFromJsonAsync<DataWrap<AgreementData>>();
        var id = created!.data.id;

        var response = await _client.PutAsJsonAsync($"/api/agreements/{id}", new { id, name = "Updated Name" });
        response.StatusCode.Should().Be(HttpStatusCode.OK);
    }

    [Fact]
    public async Task Destroy_soft_deletes()
    {
        await AuthenticateAsync();
        var code = Guid.NewGuid().ToString("N")[..10];
        var create = await _client.PostAsJsonAsync("/api/agreements", new { code, name = "ToDelete", isActive = true });
        var created = await create.Content.ReadFromJsonAsync<DataWrap<AgreementData>>();
        var response = await _client.DeleteAsync($"/api/agreements/{created!.data.id}");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
    }

    private record AgreementData(long id, string code, string name, string? description, bool isActive, DateTime createdAt, DateTime updatedAt, DateTime? deletedAt);
    private record AgreementDetailData(long id, string code, string name, string? description, bool isActive, DateTime createdAt, DateTime updatedAt, DateTime? deletedAt, int providersCount);
    private record DataWrap<T>(bool success, T data, string message);
    private record DetailWrap(bool success, AgreementDetailData data, string message);
    private record PaginatedWrap<T>(bool success, PaginatedItem<T> data, string message);
    private record PaginatedItem<T>(List<T> items, int page, int pageSize, int totalCount, int totalPages, bool hasPreviousPage, bool hasNextPage);
    private record LoginWrap(bool success, LoginData data, string message);
    private record LoginData(long userId, string email, string name, string? role, string token);
}

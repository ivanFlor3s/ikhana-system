using System.Net;
using System.Net.Http.Headers;
using System.Net.Http.Json;
using FluentAssertions;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.DependencyInjection;

namespace Ikhana.Api.Tests.Integration;

public class BrokerTests : IClassFixture<TestWebApplicationFactory>, IAsyncLifetime
{
    private readonly HttpClient _client;
    private readonly IServiceProvider _services;

    public BrokerTests(TestWebApplicationFactory factory)
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
    public async Task Index_returns_paginated_brokers()
    {
        await AuthenticateAsync();
        var response = await _client.GetAsync("/api/brokers?page=1&page_size=10");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<PaginatedWrap<BrokerData>>();
        content!.success.Should().BeTrue();
        content.data.totalCount.Should().BeGreaterThanOrEqualTo(5);
    }

    [Fact]
    public async Task Show_returns_broker_with_full_name()
    {
        await AuthenticateAsync();
        var email = $"broker-{Guid.NewGuid():N}@test.com";
        var create = await _client.PostAsJsonAsync("/api/brokers", new { firstName = "Juan", lastName = "Perez", email });
        var created = await create.Content.ReadFromJsonAsync<DataWrap<BrokerData>>();
        var id = created!.data.id;

        var response = await _client.GetAsync($"/api/brokers/{id}");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
        var content = await response.Content.ReadFromJsonAsync<DataWrap<BrokerData>>();
        content!.data.fullName.Should().Be("Juan Perez");
    }

    [Fact]
    public async Task Store_creates_returns_201()
    {
        await AuthenticateAsync();
        var email = $"broker-{Guid.NewGuid():N}@test.com";
        var response = await _client.PostAsJsonAsync("/api/brokers", new { firstName = "Juan", lastName = "Perez", email });
        response.StatusCode.Should().Be(HttpStatusCode.Created);
    }

    [Fact]
    public async Task Store_duplicate_email_returns_422()
    {
        await AuthenticateAsync();
        var email = $"broker-{Guid.NewGuid():N}@test.com";
        await _client.PostAsJsonAsync("/api/brokers", new { firstName = "First", lastName = "One", email });
        var response = await _client.PostAsJsonAsync("/api/brokers", new { firstName = "Second", lastName = "Two", email });
        response.StatusCode.Should().Be(HttpStatusCode.UnprocessableEntity);
    }

    [Fact]
    public async Task Update_modifies_existing()
    {
        await AuthenticateAsync();
        var email = $"broker-{Guid.NewGuid():N}@test.com";
        var create = await _client.PostAsJsonAsync("/api/brokers", new { firstName = "Original", lastName = "Name", email });
        var created = await create.Content.ReadFromJsonAsync<DataWrap<BrokerData>>();
        var response = await _client.PutAsJsonAsync($"/api/brokers/{created!.data.id}", new { id = created.data.id, firstName = "Updated" });
        response.StatusCode.Should().Be(HttpStatusCode.OK);
    }

    [Fact]
    public async Task Destroy_soft_deletes()
    {
        await AuthenticateAsync();
        var email = $"broker-{Guid.NewGuid():N}@test.com";
        var create = await _client.PostAsJsonAsync("/api/brokers", new { firstName = "X", lastName = "Y", email });
        var created = await create.Content.ReadFromJsonAsync<DataWrap<BrokerData>>();
        var response = await _client.DeleteAsync($"/api/brokers/{created!.data.id}");
        response.StatusCode.Should().Be(HttpStatusCode.OK);
    }

    private record BrokerData(long id, string firstName, string lastName, string email, string? phone, string fullName, DateTime createdAt, DateTime updatedAt, DateTime? deletedAt);
    private record DataWrap<T>(bool success, T data, string message);
    private record PaginatedWrap<T>(bool success, PaginatedItem<T> data, string message);
    private record PaginatedItem<T>(List<T> items, int page, int pageSize, int totalCount, int totalPages, bool hasPreviousPage, bool hasNextPage);
    private record LoginWrap(bool success, LoginData data, string message);
    private record LoginData(long userId, string email, string name, string? role, string token);
}

using System.Net;
using System.Net.Http.Headers;
using System.Net.Http.Json;
using FluentAssertions;
using Ikhana.Application.Features.Categories;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.DependencyInjection;

namespace Ikhana.Api.Tests.Integration;

public class CategoryTests : IClassFixture<TestWebApplicationFactory>, IAsyncLifetime
{
    private readonly HttpClient _client;
    private readonly IServiceProvider _services;

    public CategoryTests(TestWebApplicationFactory factory)
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
        {
            await roleManager.CreateAsync(new ApplicationRole { Name = "Admin", Description = "Administrator" });
        }

        var existingUser = await userManager.FindByEmailAsync("admin@ikhana.com");
        if (existingUser == null)
        {
            var user = new ApplicationUser
            {
                UserName = "admin@ikhana.com",
                Email = "admin@ikhana.com",
                Name = "Administrador",
                EmailConfirmed = true
            };

            await userManager.CreateAsync(user, "admin123");
            await userManager.AddToRoleAsync(user, "Admin");
        }
    }

    public Task DisposeAsync() => Task.CompletedTask;

    private async Task AuthenticateAsync()
    {
        var response = await _client.PostAsJsonAsync("/api/auth/login", new
        {
            email = "admin@ikhana.com",
            password = "admin123"
        });

        var content = await response.Content.ReadFromJsonAsync<LoginResponse>();
        _client.DefaultRequestHeaders.Authorization =
            new AuthenticationHeaderValue("Bearer", content!.data.token);
    }

    [Fact]
    public async Task Index_returns_paginated_categories_sorted_by_name()
    {
        await AuthenticateAsync();

        var response = await _client.GetAsync("/api/categories?page=1&page_size=10&sort_by=name&sort_order=asc");
        response.StatusCode.Should().Be(HttpStatusCode.OK);

        var content = await response.Content.ReadFromJsonAsync<PaginatedResponse>();
        content.Should().NotBeNull();
        content!.success.Should().BeTrue();
        content.data.totalCount.Should().BeGreaterThanOrEqualTo(4);
        content.data.items.Should().NotBeEmpty();
    }

    [Fact]
    public async Task Index_without_auth_returns_unauthorized()
    {
        var response = await _client.GetAsync("/api/categories");
        response.StatusCode.Should().Be(HttpStatusCode.Unauthorized);
    }

    [Fact]
    public async Task Show_returns_category_with_providers_count()
    {
        await AuthenticateAsync();

        var unique = $"Test {Guid.NewGuid():N}";
        var createResp = await _client.PostAsJsonAsync("/api/categories", new { name = unique });
        var created = await createResp.Content.ReadFromJsonAsync<CategoryWrapper>();
        var id = created!.data.id;

        var response = await _client.GetAsync($"/api/categories/{id}");
        response.StatusCode.Should().Be(HttpStatusCode.OK);

        var content = await response.Content.ReadFromJsonAsync<DetailWrapper>();
        content.Should().NotBeNull();
        content!.data.name.Should().Be(unique);
        content.data.providersCount.Should().Be(0);
    }

    [Fact]
    public async Task Show_returns_404_for_non_existent()
    {
        await AuthenticateAsync();

        var response = await _client.GetAsync("/api/categories/99999");
        response.StatusCode.Should().Be(HttpStatusCode.NotFound);
    }

    [Fact]
    public async Task Store_creates_category_returns_201()
    {
        await AuthenticateAsync();

        var unique = $"Test {Guid.NewGuid():N}";
        var response = await _client.PostAsJsonAsync("/api/categories", new
        {
            name = unique,
            description = "Integration test"
        });

        response.StatusCode.Should().Be(HttpStatusCode.Created);

        var content = await response.Content.ReadFromJsonAsync<CategoryWrapper>();
        content.Should().NotBeNull();
        content!.success.Should().BeTrue();
        content.data.name.Should().Be(unique);
        content.data.description.Should().Be("Integration test");
    }

    [Fact]
    public async Task Store_duplicate_name_returns_422()
    {
        await AuthenticateAsync();

        var unique = $"Test {Guid.NewGuid():N}";
        await _client.PostAsJsonAsync("/api/categories", new { name = unique });

        var response = await _client.PostAsJsonAsync("/api/categories", new { name = unique });

        response.StatusCode.Should().Be(HttpStatusCode.UnprocessableEntity);

        var content = await response.Content.ReadFromJsonAsync<ValidationErrorWrapper>();
        content.Should().NotBeNull();
        content!.success.Should().BeFalse();
        content.message.Should().Be("Validation error");
        content.errors.Should().ContainKey("Name");
    }

    [Fact]
    public async Task Update_modifies_existing_category()
    {
        await AuthenticateAsync();

        var unique = $"Test {Guid.NewGuid():N}";
        var createResp = await _client.PostAsJsonAsync("/api/categories", new { name = unique });
        var created = await createResp.Content.ReadFromJsonAsync<CategoryWrapper>();
        var id = created!.data.id;

        var response = await _client.PutAsJsonAsync($"/api/categories/{id}", new
        {
            id,
            name = $"{unique} Updated",
            description = "Updated description"
        });

        response.StatusCode.Should().Be(HttpStatusCode.OK);

        var content = await response.Content.ReadFromJsonAsync<CategoryWrapper>();
        content!.data.name.Should().Be($"{unique} Updated");
    }

    [Fact]
    public async Task Update_returns_404_for_non_existent()
    {
        await AuthenticateAsync();

        var response = await _client.PutAsJsonAsync("/api/categories/99999", new
        {
            id = 99999,
            name = "X"
        });

        response.StatusCode.Should().Be(HttpStatusCode.NotFound);
    }

    [Fact]
    public async Task Destroy_soft_deletes_category()
    {
        await AuthenticateAsync();

        var unique = $"Test {Guid.NewGuid():N}";
        var createResp = await _client.PostAsJsonAsync("/api/categories", new { name = unique });
        var created = await createResp.Content.ReadFromJsonAsync<CategoryWrapper>();
        var id = created!.data.id;

        var response = await _client.DeleteAsync($"/api/categories/{id}");
        response.StatusCode.Should().Be(HttpStatusCode.OK);

        var content = await response.Content.ReadFromJsonAsync<MessageWrapper>();
        content!.success.Should().BeTrue();

        var showResponse = await _client.GetAsync($"/api/categories/{id}");
        showResponse.StatusCode.Should().Be(HttpStatusCode.NotFound);
    }

    [Fact]
    public async Task Destroy_returns_404_for_non_existent()
    {
        await AuthenticateAsync();

        var response = await _client.DeleteAsync("/api/categories/99999");
        response.StatusCode.Should().Be(HttpStatusCode.NotFound);
    }

    private record CategoryData(long id, string name, string? description, DateTime createdAt, DateTime updatedAt, DateTime? deletedAt);
    private record CategoryDetailData(long id, string name, string? description, DateTime createdAt, DateTime updatedAt, DateTime? deletedAt, int providersCount);
    private record CategoryWrapper(bool success, CategoryData data, string message);
    private record DetailWrapper(bool success, CategoryDetailData data, string message);
    private record PaginatedItem(List<CategoryData> items, int page, int pageSize, int totalCount, int totalPages, bool hasPreviousPage, bool hasNextPage);
    private record PaginatedResponse(bool success, PaginatedItem data, string message);
    private record MessageWrapper(bool success, string? data, string message);
    private record ValidationErrorWrapper(bool success, string message, Dictionary<string, string[]> errors);
    private record LoginData(long userId, string email, string name, string? role, string token);
    private record LoginResponse(bool success, LoginData data, string message);
}

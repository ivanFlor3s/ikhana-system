using System.Net;
using System.Net.Http.Headers;
using System.Net.Http.Json;
using FluentAssertions;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Identity;
using Microsoft.Extensions.DependencyInjection;

namespace Ikhana.Api.Tests.Integration;

public class AuthTests : IClassFixture<TestWebApplicationFactory>, IAsyncLifetime
{
    private readonly HttpClient _client;
    private readonly IServiceProvider _services;

    public AuthTests(TestWebApplicationFactory factory)
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

    [Fact]
    public async Task Login_with_valid_credentials_returns_token()
    {
        var response = await _client.PostAsJsonAsync("/api/auth/login", new
        {
            email = "admin@ikhana.com",
            password = "admin123"
        });

        response.StatusCode.Should().Be(HttpStatusCode.OK);

        var content = await response.Content.ReadFromJsonAsync<LoginResponse>();
        content.Should().NotBeNull();
        content!.success.Should().BeTrue();
        content.data.token.Should().NotBeNullOrEmpty();
        content.data.email.Should().Be("admin@ikhana.com");
        content.data.name.Should().Be("Administrador");
        content.data.role.Should().Be("Admin");
    }

    [Fact]
    public async Task Login_with_invalid_password_returns_unauthorized()
    {
        var response = await _client.PostAsJsonAsync("/api/auth/login", new
        {
            email = "admin@ikhana.com",
            password = "wrongpassword"
        });

        response.StatusCode.Should().Be(HttpStatusCode.Unauthorized);
    }

    [Fact]
    public async Task Login_with_nonexistent_user_returns_unauthorized()
    {
        var response = await _client.PostAsJsonAsync("/api/auth/login", new
        {
            email = "noone@ikhana.com",
            password = "anything"
        });

        response.StatusCode.Should().Be(HttpStatusCode.Unauthorized);
    }

    [Fact]
    public async Task Me_with_valid_token_returns_user_info()
    {
        var token = await GetAuthToken();

        _client.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", token);
        var response = await _client.GetAsync("/api/auth/me");

        response.StatusCode.Should().Be(HttpStatusCode.OK);

        var content = await response.Content.ReadFromJsonAsync<MeResponse>();
        content.Should().NotBeNull();
        content!.data.email.Should().Be("admin@ikhana.com");
        content.data.role.Should().Be("Admin");
    }

    [Fact]
    public async Task Me_without_token_returns_unauthorized()
    {
        _client.DefaultRequestHeaders.Authorization = null;
        var response = await _client.GetAsync("/api/auth/me");

        response.StatusCode.Should().Be(HttpStatusCode.Unauthorized);
    }

    [Fact]
    public async Task Logout_with_valid_token_returns_ok()
    {
        var token = await GetAuthToken();

        _client.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", token);
        var response = await _client.PostAsync("/api/auth/logout", null);

        response.StatusCode.Should().Be(HttpStatusCode.OK);
    }

    private async Task<string> GetAuthToken()
    {
        var response = await _client.PostAsJsonAsync("/api/auth/login", new
        {
            email = "admin@ikhana.com",
            password = "admin123"
        });

        var content = await response.Content.ReadFromJsonAsync<LoginResponse>();
        return content!.data.token;
    }

    private record LoginData(long userId, string email, string name, string? role, string token);
    private record LoginResponse(bool success, LoginData data, string message);
    private record MeData(long id, string email, string name, string? role);
    private record MeResponse(bool success, MeData data, string message);
}

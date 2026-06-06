namespace Ikhana.Application.Common.Interfaces;

public interface IAuthService
{
    Task<AuthResponse?> LoginAsync(string email, string password);
}

public record AuthResponse(long UserId, string Email, string Name, string? Role, string Token);

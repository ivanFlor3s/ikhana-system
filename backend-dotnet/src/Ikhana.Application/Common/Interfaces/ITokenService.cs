namespace Ikhana.Application.Common.Interfaces;

public interface ITokenService
{
    string GenerateToken(long userId, string email, string name, string? role);
}

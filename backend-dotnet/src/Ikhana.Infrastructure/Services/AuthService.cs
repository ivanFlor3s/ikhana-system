using Ikhana.Application.Common.Interfaces;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Identity;

namespace Ikhana.Infrastructure.Services;

public class AuthService : IAuthService
{
    private readonly UserManager<ApplicationUser> _userManager;
    private readonly ITokenService _tokenService;

    public AuthService(UserManager<ApplicationUser> userManager, ITokenService tokenService)
    {
        _userManager = userManager;
        _tokenService = tokenService;
    }

    public async Task<AuthResponse?> LoginAsync(string email, string password)
    {
        var user = await _userManager.FindByEmailAsync(email);
        if (user == null) return null;

        var valid = await _userManager.CheckPasswordAsync(user, password);
        if (!valid) return null;

        var roles = await _userManager.GetRolesAsync(user);
        var role = roles.FirstOrDefault();

        var token = _tokenService.GenerateToken(user.Id, user.Email!, user.Name, role);

        return new AuthResponse(user.Id, user.Email!, user.Name, role, token);
    }
}

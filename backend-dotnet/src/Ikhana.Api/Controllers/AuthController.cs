using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using Ikhana.Domain.Entities;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Mvc;

namespace Ikhana.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
public class AuthController : ControllerBase
{
    private readonly IAuthService _authService;
    private readonly UserManager<ApplicationUser> _userManager;

    public AuthController(IAuthService authService, UserManager<ApplicationUser> userManager)
    {
        _authService = authService;
        _userManager = userManager;
    }

    [HttpPost("login")]
    [ProducesResponseType(typeof(ApiResponse<AuthResponse>), 200)]
    [ProducesResponseType(401)]
    public async Task<ActionResult<ApiResponse<AuthResponse>>> Login([FromBody] LoginRequest request)
    {
        var result = await _authService.LoginAsync(request.Email, request.Password);
        if (result == null)
            return Unauthorized(ApiResponse<AuthResponse>.Fail("Invalid credentials"));

        return Ok(ApiResponse<AuthResponse>.Ok(result, "Login successful"));
    }

    [HttpPost("logout")]
    [Authorize]
    [ProducesResponseType(typeof(ApiResponse<object?>), 200)]
    public IActionResult Logout()
    {
        return Ok(ApiResponse<object?>.Ok(null, "Logged out successfully"));
    }

    [HttpGet("me")]
    [Authorize]
    [ProducesResponseType(typeof(ApiResponse<MeResponse>), 200)]
    [ProducesResponseType(401)]
    [ProducesResponseType(404)]
    public async Task<ActionResult<ApiResponse<MeResponse>>> Me()
    {
        var userId = User.FindFirstValue(ClaimTypes.NameIdentifier);
        if (userId == null)
            return Unauthorized();

        var user = await _userManager.FindByIdAsync(userId);
        if (user == null)
            return NotFound();

        var roles = await _userManager.GetRolesAsync(user);

        var me = new MeResponse(
            user.Id,
            user.Email!,
            user.Name,
            roles.FirstOrDefault()
        );

        return Ok(ApiResponse<MeResponse>.Ok(me, "Current user retrieved"));
    }
}

public record LoginRequest(string Email, string Password);
public record MeResponse(long Id, string Email, string Name, string? Role);

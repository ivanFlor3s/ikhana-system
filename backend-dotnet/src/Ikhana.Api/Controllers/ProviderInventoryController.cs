using Ikhana.Application.Common.Models;
using Ikhana.Application.Features.Providers;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace Ikhana.Api.Controllers;

[ApiController]
[Route("api/providers/{providerId}/inventory")]
[Authorize]
public class ProviderInventoryController : ControllerBase
{
    private readonly ISender _sender;

    public ProviderInventoryController(ISender sender)
    {
        _sender = sender;
    }

    [HttpGet]
    public async Task<ActionResult<ApiResponse<ProviderInventoryResponse>>> Show(long providerId)
    {
        var result = await _sender.Send(new GetProviderInventoryQuery(providerId));
        return Ok(ApiResponse<ProviderInventoryResponse>.Ok(result, "Inventario del proveedor obtenido exitosamente"));
    }

    [HttpPost]
    public async Task<ActionResult<ApiResponse<ProviderInventoryResponse>>> Create(
        long providerId, [FromBody] CreateProviderInventoryCommand command)
    {
        if (providerId != command.ProviderId)
            return BadRequest(ApiResponse<ProviderInventoryResponse?>.Fail("El ID de la URL no coincide con el ID del cuerpo"));

        try
        {
            var result = await _sender.Send(command);
            return CreatedAtAction(nameof(Show), new { providerId = result.ProviderId },
                ApiResponse<ProviderInventoryResponse>.Ok(result, "Registro de bobinas creado exitosamente"));
        }
        catch (InvalidOperationException ex)
        {
            return Conflict(ApiResponse<ProviderInventoryResponse?>.Fail(ex.Message));
        }
    }

    [HttpPut]
    public async Task<ActionResult<ApiResponse<ProviderInventoryResponse>>> Update(
        long providerId, [FromBody] UpdateProviderInventoryCommand command)
    {
        if (providerId != command.ProviderId)
            return BadRequest(ApiResponse<ProviderInventoryResponse?>.Fail("El ID de la URL no coincide con el ID del cuerpo"));

        var result = await _sender.Send(command);
        if (result == null)
            return NotFound(ApiResponse<ProviderInventoryResponse?>.Fail("El proveedor no tiene un inventario de bobinas"));
        return Ok(ApiResponse<ProviderInventoryResponse>.Ok(result, "Registro de bobinas actualizado exitosamente"));
    }
}

using Ikhana.Application.Common.Models;
using Ikhana.Application.Features.Providers;
using Ikhana.Application.Features.Providers.GetSummary;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace Ikhana.Api.Controllers;

[ApiController]
[Route("api/providers")]
[Authorize]
public class ProvidersController : ControllerBase
{
    private readonly ISender _sender;

    public ProvidersController(ISender sender)
    {
        _sender = sender;
    }

    [HttpGet]
    public async Task<ActionResult<ApiResponse<PaginatedList<ProviderListResponse>>>> Index(
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 15,
        [FromQuery] string? search = null,
        [FromQuery] long? categoryId = null)
    {
        var result = await _sender.Send(new GetProvidersQuery(page, pageSize, search, categoryId));
        return Ok(ApiResponse<PaginatedList<ProviderListResponse>>.Ok(result, "Proveedores obtenidos exitosamente"));
    }

    [HttpGet("summary")]
    public async Task<ActionResult<ApiResponse<List<ProviderSummaryResponse>>>> GetAllProvidersSummary()
    {
        var result = await _sender.Send(new GetProvidersSummaryQuery());
        return Ok(ApiResponse<List<ProviderSummaryResponse>>.Ok(result, "Proveedores obtenidos exitosamente"));
    }

    [HttpGet("coil-summary")]
    public async Task<ActionResult<ApiResponse<PaginatedList<CoilSummaryResponse>>>> CoilSummary(
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 15,
        [FromQuery] string? search = null,
        [FromQuery] string sortBy = "provider_name",
        [FromQuery] string sortDir = "asc")
    {
        var result = await _sender.Send(new GetCoilSummaryQuery(page, pageSize, search, sortBy, sortDir));
        return Ok(ApiResponse<PaginatedList<CoilSummaryResponse>>.Ok(result, "Resumen de bobinas por proveedor obtenido exitosamente"));
    }

    [HttpPost]
    public async Task<ActionResult<ApiResponse<ProviderDetailResponse>>> Store([FromBody] CreateProviderCommand command)
    {
        var result = await _sender.Send(command);
        return CreatedAtAction(nameof(Show), new { id = result.Id },
            ApiResponse<ProviderDetailResponse>.Ok(result, "Proveedor creado exitosamente"));
    }

    [HttpGet("{id}")]
    public async Task<ActionResult<ApiResponse<ProviderDetailResponse>>> Show(long id)
    {
        var result = await _sender.Send(new GetProviderByIdQuery(id));
        if (result == null)
            return NotFound(ApiResponse<ProviderDetailResponse?>.Fail("Proveedor no encontrado"));
        return Ok(ApiResponse<ProviderDetailResponse>.Ok(result, "Proveedor obtenido exitosamente"));
    }

    [HttpPut("{id}")]
    public async Task<ActionResult<ApiResponse<ProviderDetailResponse>>> Update(
        long id, [FromBody] UpdateProviderCommand command)
    {
        if (id != command.Id)
            return BadRequest(ApiResponse<ProviderDetailResponse?>.Fail("El ID de la URL no coincide con el ID del cuerpo"));

        var result = await _sender.Send(command);
        if (result == null)
            return NotFound(ApiResponse<ProviderDetailResponse?>.Fail("Proveedor no encontrado"));
        return Ok(ApiResponse<ProviderDetailResponse>.Ok(result, "Proveedor actualizado exitosamente"));
    }

    [HttpDelete("{id}")]
    public async Task<ActionResult<ApiResponse<object?>>> Destroy(long id)
    {
        var result = await _sender.Send(new DeleteProviderCommand(id));
        if (!result)
            return NotFound(ApiResponse<object?>.Fail("Proveedor no encontrado"));
        return Ok(ApiResponse<object?>.Ok(null, "Proveedor eliminado exitosamente"));
    }
}

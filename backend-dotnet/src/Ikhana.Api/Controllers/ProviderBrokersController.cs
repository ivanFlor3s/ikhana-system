using Ikhana.Application.Common.Models;
using Ikhana.Application.Features.Providers;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace Ikhana.Api.Controllers;

[ApiController]
[Route("api/providers/{providerId}/brokers")]
[Authorize]
public class ProviderBrokersController : ControllerBase
{
    private readonly ISender _sender;

    public ProviderBrokersController(ISender sender)
    {
        _sender = sender;
    }

    [HttpPost]
    public async Task<ActionResult<ApiResponse<BrokerResponse>>> Create(
        long providerId, [FromBody] CreateProviderBrokerCommand command)
    {
        if (providerId != command.ProviderId)
            return BadRequest(ApiResponse<BrokerResponse?>.Fail("El ID de la URL no coincide con el ID del cuerpo"));

        var result = await _sender.Send(command);
        return CreatedAtAction(nameof(Create),
            new { providerId = providerId },
            ApiResponse<BrokerResponse>.Ok(result, "Corredor creado exitosamente"));
    }
}

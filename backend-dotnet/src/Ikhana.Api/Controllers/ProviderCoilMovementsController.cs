using Ikhana.Application.Common.Models;
using Ikhana.Application.Features.Providers;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace Ikhana.Api.Controllers;

[ApiController]
[Route("api/providers/{providerId}/coil-movements")]
[Authorize]
public class ProviderCoilMovementsController : ControllerBase
{
    private readonly ISender _sender;

    public ProviderCoilMovementsController(ISender sender)
    {
        _sender = sender;
    }

    [HttpGet]
    public async Task<ActionResult<ApiResponse<PaginatedList<CoilMovementResponse>>>> Movements(
        long providerId,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 15,
        [FromQuery] string? dateFrom = null,
        [FromQuery] string? dateTo = null,
        [FromQuery] string sortBy = "date",
        [FromQuery] string sortDir = "desc")
    {
        var result = await _sender.Send(new GetCoilMovementsQuery(providerId, page, pageSize, dateFrom, dateTo, sortBy, sortDir));
        return Ok(ApiResponse<PaginatedList<CoilMovementResponse>>.Ok(result, "Movimientos de bobinas obtenidos exitosamente"));
    }
}

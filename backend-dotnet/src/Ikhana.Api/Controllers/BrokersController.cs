using Ikhana.Application.Common.Models;
using Ikhana.Application.Features.Brokers;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace Ikhana.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class BrokersController : ControllerBase
{
    private readonly ISender _sender;

    public BrokersController(ISender sender)
    {
        _sender = sender;
    }

    [HttpGet]
    public async Task<ActionResult<ApiResponse<PaginatedList<BrokerResponse>>>> Index(
        [FromQuery] int page = 1,
        [FromQuery] int page_size = 10,
        [FromQuery] string sort_by = "last_name",
        [FromQuery] string sort_order = "asc")
    {
        var result = await _sender.Send(new GetBrokersQuery(page, page_size, sort_by, sort_order));
        return Ok(ApiResponse<PaginatedList<BrokerResponse>>.Ok(result, "Brokers retrieved successfully"));
    }

    [HttpGet("{id}")]
    public async Task<ActionResult<ApiResponse<BrokerResponse>>> Show(long id)
    {
        var result = await _sender.Send(new GetBrokerByIdQuery(id));
        if (result == null)
            return NotFound(ApiResponse<BrokerResponse>.Fail("Broker not found"));

        return Ok(ApiResponse<BrokerResponse>.Ok(result, "Broker retrieved successfully"));
    }

    [HttpPost]
    public async Task<ActionResult<ApiResponse<BrokerResponse>>> Store([FromBody] CreateBrokerCommand command)
    {
        var result = await _sender.Send(command);
        return CreatedAtAction(nameof(Show), new { id = result.Id },
            ApiResponse<BrokerResponse>.Ok(result, "Broker created successfully"));
    }

    [HttpPut("{id}")]
    public async Task<ActionResult<ApiResponse<BrokerResponse>>> Update(long id, [FromBody] UpdateBrokerCommand command)
    {
        if (id != command.Id)
            return BadRequest(ApiResponse<BrokerResponse>.Fail("Id mismatch"));

        var result = await _sender.Send(command);
        if (result == null)
            return NotFound(ApiResponse<BrokerResponse>.Fail("Broker not found"));

        return Ok(ApiResponse<BrokerResponse>.Ok(result, "Broker updated successfully"));
    }

    [HttpDelete("{id}")]
    public async Task<ActionResult<ApiResponse<object?>>> Destroy(long id)
    {
        var result = await _sender.Send(new DeleteBrokerCommand(id));
        if (!result)
            return NotFound(ApiResponse<object?>.Fail("Broker not found"));

        return Ok(ApiResponse<object?>.Ok(null!, "Broker deleted successfully"));
    }
}

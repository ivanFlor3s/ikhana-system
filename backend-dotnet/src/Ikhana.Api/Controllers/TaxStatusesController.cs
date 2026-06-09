using Ikhana.Application.Common.Models;
using Ikhana.Application.Features.TaxStatuses;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace Ikhana.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class TaxStatusesController : ControllerBase
{
    private readonly ISender _sender;

    public TaxStatusesController(ISender sender)
    {
        _sender = sender;
    }

    [HttpGet]
    public async Task<ActionResult<ApiResponse<PaginatedList<TaxStatusResponse>>>> Index(
        [FromQuery] int page = 1,
        [FromQuery] int page_size = 10,
        [FromQuery] string sort_by = "name",
        [FromQuery] string sort_order = "asc")
    {
        var result = await _sender.Send(new GetTaxStatusesQuery(page, page_size, sort_by, sort_order));
        return Ok(ApiResponse<PaginatedList<TaxStatusResponse>>.Ok(result, "Tax statuses retrieved successfully"));
    }

    [HttpGet("{id}")]
    public async Task<ActionResult<ApiResponse<TaxStatusDetailResponse>>> Show(long id)
    {
        var result = await _sender.Send(new GetTaxStatusByIdQuery(id));
        if (result == null)
            return NotFound(ApiResponse<TaxStatusDetailResponse>.Fail("Tax status not found"));

        return Ok(ApiResponse<TaxStatusDetailResponse>.Ok(result, "Tax status retrieved successfully"));
    }

    [HttpPost]
    public async Task<ActionResult<ApiResponse<TaxStatusResponse>>> Store([FromBody] CreateTaxStatusCommand command)
    {
        var result = await _sender.Send(command);
        return CreatedAtAction(nameof(Show), new { id = result.Id },
            ApiResponse<TaxStatusResponse>.Ok(result, "Tax status created successfully"));
    }

    [HttpPut("{id}")]
    public async Task<ActionResult<ApiResponse<TaxStatusResponse>>> Update(long id, [FromBody] UpdateTaxStatusCommand command)
    {
        if (id != command.Id)
            return BadRequest(ApiResponse<TaxStatusResponse>.Fail("Id mismatch"));

        var result = await _sender.Send(command);
        if (result == null)
            return NotFound(ApiResponse<TaxStatusResponse>.Fail("Tax status not found"));

        return Ok(ApiResponse<TaxStatusResponse>.Ok(result, "Tax status updated successfully"));
    }

    [HttpDelete("{id}")]
    public async Task<ActionResult<ApiResponse<object?>>> Destroy(long id)
    {
        var result = await _sender.Send(new DeleteTaxStatusCommand(id));
        if (!result)
            return NotFound(ApiResponse<object?>.Fail("Tax status not found"));

        return Ok(ApiResponse<object?>.Ok(null!, "Tax status deleted successfully"));
    }
}

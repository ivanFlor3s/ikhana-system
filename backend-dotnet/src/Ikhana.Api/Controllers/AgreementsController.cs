using Ikhana.Application.Common.Models;
using Ikhana.Application.Features.Agreements;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace Ikhana.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class AgreementsController : ControllerBase
{
    private readonly ISender _sender;

    public AgreementsController(ISender sender)
    {
        _sender = sender;
    }

    [HttpGet]
    public async Task<ActionResult<ApiResponse<PaginatedList<AgreementResponse>>>> Index(
        [FromQuery] int page = 1,
        [FromQuery] int page_size = 10,
        [FromQuery] string sort_by = "name",
        [FromQuery] string sort_order = "asc")
    {
        var result = await _sender.Send(new GetAgreementsQuery(page, page_size, sort_by, sort_order));
        return Ok(ApiResponse<PaginatedList<AgreementResponse>>.Ok(result, "Agreements retrieved successfully"));
    }

    [HttpGet("{id}")]
    public async Task<ActionResult<ApiResponse<AgreementDetailResponse>>> Show(long id)
    {
        var result = await _sender.Send(new GetAgreementByIdQuery(id));
        if (result == null)
            return NotFound(ApiResponse<AgreementDetailResponse>.Fail("Agreement not found"));

        return Ok(ApiResponse<AgreementDetailResponse>.Ok(result, "Agreement retrieved successfully"));
    }

    [HttpPost]
    public async Task<ActionResult<ApiResponse<AgreementResponse>>> Store([FromBody] CreateAgreementCommand command)
    {
        var result = await _sender.Send(command);
        return CreatedAtAction(nameof(Show), new { id = result.Id },
            ApiResponse<AgreementResponse>.Ok(result, "Agreement created successfully"));
    }

    [HttpPut("{id}")]
    public async Task<ActionResult<ApiResponse<AgreementResponse>>> Update(long id, [FromBody] UpdateAgreementCommand command)
    {
        if (id != command.Id)
            return BadRequest(ApiResponse<AgreementResponse>.Fail("Id mismatch"));

        var result = await _sender.Send(command);
        if (result == null)
            return NotFound(ApiResponse<AgreementResponse>.Fail("Agreement not found"));

        return Ok(ApiResponse<AgreementResponse>.Ok(result, "Agreement updated successfully"));
    }

    [HttpDelete("{id}")]
    public async Task<ActionResult<ApiResponse<object?>>> Destroy(long id)
    {
        var result = await _sender.Send(new DeleteAgreementCommand(id));
        if (!result)
            return NotFound(ApiResponse<object?>.Fail("Agreement not found"));

        return Ok(ApiResponse<object?>.Ok(null!, "Agreement deleted successfully"));
    }
}

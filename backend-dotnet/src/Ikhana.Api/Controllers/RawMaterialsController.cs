using Ikhana.Application.Common.Models;
using Ikhana.Application.Features.RawMaterials.GetCharacteristics;
using Ikhana.Application.Features.RawMaterials.GetDiameters;
using Ikhana.Application.Features.RawMaterials.GetTypes;
using Ikhana.Application.Features.RawMaterials.ValidateResistance;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace Ikhana.Api.Controllers;

[ApiController]
[Route("api/raw-materials")]
[Authorize]
public class RawMaterialsController : ControllerBase
{
    private readonly ISender _sender;

    public RawMaterialsController(ISender sender)
    {
        _sender = sender;
    }

    [HttpGet("types")]
    public async Task<ActionResult<ApiResponse<List<RawMaterialTypeResponse>>>> IndexTypes()
    {
        var result = await _sender.Send(new GetRawMaterialTypesQuery());
        return Ok(ApiResponse<List<RawMaterialTypeResponse>>.Ok(result, "Raw material types retrieved successfully"));
    }

    [HttpGet("types/{typeId}/characteristics")]
    public async Task<ActionResult<ApiResponse<List<RawMaterialCharacteristicResponse>>>> IndexCharacteristics(long typeId)
    {
        var result = await _sender.Send(new GetCharacteristicsQuery(typeId));
        return Ok(ApiResponse<List<RawMaterialCharacteristicResponse>>.Ok(result, "Characteristics retrieved successfully"));
    }

    [HttpPost("validate-resistance")]
    public async Task<ActionResult<ApiResponse<ValidateResistanceResult>>> ValidateResistance([FromBody] ValidateResistanceCommand command)
    {
        var result = await _sender.Send(command);
        var message = result.HasRule
            ? (result.Valid ? "Value within IRAM norm" : "Value exceeds IRAM maximum")
            : "No IRAM rule associated with this characteristic";
        return Ok(ApiResponse<ValidateResistanceResult>.Ok(result, message));
    }

    [HttpGet("diameters-and-iram")]
    public async Task<ActionResult<ApiResponse<List<DiameterResponse>>>> GetDiametersAndIram()
    {
        var result = await _sender.Send(new GetDiametersQuery());
        return Ok(ApiResponse<List<DiameterResponse>>.Ok(result, "Diameters and IRAM resistance retrieved successfully"));
    }
}

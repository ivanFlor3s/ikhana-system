using Ikhana.Application.Common.Models;
using Ikhana.Application.Features.Categories;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace Ikhana.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class CategoriesController : ControllerBase
{
    private readonly ISender _sender;

    public CategoriesController(ISender sender)
    {
        _sender = sender;
    }

    [HttpGet]
    public async Task<ActionResult<ApiResponse<PaginatedList<CategoryResponse>>>> Index(
        [FromQuery] int page = 1,
        [FromQuery] int page_size = 10,
        [FromQuery] string sort_by = "name",
        [FromQuery] string sort_order = "asc")
    {
        var result = await _sender.Send(new GetCategoriesQuery(page, page_size, sort_by, sort_order));
        return Ok(ApiResponse<PaginatedList<CategoryResponse>>.Ok(result, "Categories retrieved successfully"));
    }

    [HttpGet("{id}")]
    public async Task<ActionResult<ApiResponse<CategoryDetailResponse>>> Show(long id)
    {
        var result = await _sender.Send(new GetCategoryByIdQuery(id));
        if (result == null)
            return NotFound(ApiResponse<CategoryDetailResponse>.Fail("Category not found"));

        return Ok(ApiResponse<CategoryDetailResponse>.Ok(result, "Category retrieved successfully"));
    }

    [HttpPost]
    public async Task<ActionResult<ApiResponse<CategoryResponse>>> Store([FromBody] CreateCategoryCommand command)
    {
        var result = await _sender.Send(command);
        return CreatedAtAction(nameof(Show), new { id = result.Id },
            ApiResponse<CategoryResponse>.Ok(result, "Category created successfully"));
    }

    [HttpPut("{id}")]
    public async Task<ActionResult<ApiResponse<CategoryResponse>>> Update(long id, [FromBody] UpdateCategoryCommand command)
    {
        if (id != command.Id)
            return BadRequest(ApiResponse<CategoryResponse>.Fail("Id mismatch"));

        var result = await _sender.Send(command);
        if (result == null)
            return NotFound(ApiResponse<CategoryResponse>.Fail("Category not found"));

        return Ok(ApiResponse<CategoryResponse>.Ok(result, "Category updated successfully"));
    }

    [HttpDelete("{id}")]
    public async Task<ActionResult<ApiResponse<object?>>> Destroy(long id)
    {
        var result = await _sender.Send(new DeleteCategoryCommand(id));
        if (!result)
            return NotFound(ApiResponse<object?>.Fail("Category not found"));

        return Ok(ApiResponse<object?>.Ok(null!, "Category deleted successfully"));
    }
}

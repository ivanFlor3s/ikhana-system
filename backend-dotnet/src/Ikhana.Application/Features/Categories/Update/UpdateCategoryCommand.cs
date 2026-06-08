using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Categories;

public record UpdateCategoryCommand(long Id, string? Name, string? Description) : IRequest<CategoryResponse?>;

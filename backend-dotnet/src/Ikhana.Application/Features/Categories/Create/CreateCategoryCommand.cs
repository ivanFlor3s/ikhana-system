using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Categories;

public record CreateCategoryCommand(string Name, string? Description) : IRequest<CategoryResponse>;

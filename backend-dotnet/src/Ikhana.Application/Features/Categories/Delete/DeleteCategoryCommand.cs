using MediatR;

namespace Ikhana.Application.Features.Categories;

public record DeleteCategoryCommand(long Id) : IRequest<bool>;

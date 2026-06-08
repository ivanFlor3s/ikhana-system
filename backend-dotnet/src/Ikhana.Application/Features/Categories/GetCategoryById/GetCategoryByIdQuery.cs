using Ikhana.Application.Common.Models;
using MediatR;

namespace Ikhana.Application.Features.Categories;

public record GetCategoryByIdQuery(long Id) : IRequest<CategoryDetailResponse?>;

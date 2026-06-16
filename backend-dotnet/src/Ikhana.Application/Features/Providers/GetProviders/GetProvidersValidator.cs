using FluentValidation;

namespace Ikhana.Application.Features.Providers;

public class GetProvidersValidator : AbstractValidator<GetProvidersQuery>
{
    public GetProvidersValidator()
    {
        RuleFor(x => x.Page)
            .GreaterThanOrEqualTo(1);

        RuleFor(x => x.PageSize)
            .InclusiveBetween(1, 100);
    }
}

using FluentValidation;

namespace Ikhana.Application.Features.Providers;

public class CreateProviderInventoryValidator : AbstractValidator<CreateProviderInventoryCommand>
{
    public CreateProviderInventoryValidator()
    {
        RuleFor(x => x.CoilsAmount)
            .GreaterThanOrEqualTo(0).WithMessage("The coils amount must be at least 0.");
    }
}

using FluentValidation;
using Ikhana.Application.Common.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Providers;

public class CreateProviderValidator : AbstractValidator<CreateProviderCommand>
{
    private readonly IAppDbContext _context;

    public CreateProviderValidator(IAppDbContext context)
    {
        _context = context;

        RuleFor(x => x.FantasyName)
            .MaximumLength(255);

        RuleFor(x => x.BusinessName)
            .MaximumLength(255);

        RuleFor(x => x.Cuit)
            .MaximumLength(20)
            .MustAsync(BeUniqueCuit).WithMessage("The CUIT has already been taken.");

        RuleFor(x => x.Iibb)
            .MaximumLength(50);

        RuleFor(x => x.TaxStatusId)
            .MustAsync(ExistTaxStatus).WithMessage("The selected tax status is invalid.");

        RuleFor(x => x.AgreementId)
            .MustAsync(ExistAgreement).WithMessage("The selected agreement is invalid.");

        RuleFor(x => x.CategoryIds)
            .MustAsync(AllCategoriesExist).WithMessage("One or more selected categories are invalid.");

        RuleFor(x => x.BrokerIds)
            .MustAsync(AllBrokersExist).WithMessage("One or more selected brokers are invalid.");

        RuleFor(x => x.Phone)
            .MaximumLength(50);

        RuleFor(x => x.Email)
            .MaximumLength(255)
            .EmailAddress();

        RuleFor(x => x.Website)
            .MaximumLength(255);

        RuleFor(x => x.ContactName)
            .MaximumLength(255);

        RuleFor(x => x.BusinessHoursStart)
            .Must(BeValidTime).WithMessage("The business hours start must be a valid time (HH:mm).");

        RuleFor(x => x.BusinessHoursEnd)
            .Must(BeValidTime).WithMessage("The business hours end must be a valid time (HH:mm).");
    }

    private async Task<bool> BeUniqueCuit(string? cuit, CancellationToken cancellationToken)
    {
        if (string.IsNullOrWhiteSpace(cuit))
            return true;

        return !await _context.Providers
            .AnyAsync(p => p.Cuit == cuit && p.DeletedAt == null, cancellationToken);
    }

    private async Task<bool> ExistTaxStatus(long? taxStatusId, CancellationToken cancellationToken)
    {
        if (!taxStatusId.HasValue)
            return true;

        return await _context.TaxStatuses
            .AnyAsync(t => t.Id == taxStatusId.Value && t.DeletedAt == null, cancellationToken);
    }

    private async Task<bool> ExistAgreement(long? agreementId, CancellationToken cancellationToken)
    {
        if (!agreementId.HasValue)
            return true;

        return await _context.Agreements
            .AnyAsync(a => a.Id == agreementId.Value && a.DeletedAt == null, cancellationToken);
    }

    private async Task<bool> AllCategoriesExist(List<long>? categoryIds, CancellationToken cancellationToken)
    {
        if (categoryIds == null || categoryIds.Count == 0)
            return true;

        var distinctIds = categoryIds.Distinct().ToList();
        var existingCount = await _context.Categories
            .CountAsync(c => distinctIds.Contains(c.Id) && c.DeletedAt == null, cancellationToken);

        return existingCount == distinctIds.Count;
    }

    private async Task<bool> AllBrokersExist(List<long>? brokerIds, CancellationToken cancellationToken)
    {
        if (brokerIds == null || brokerIds.Count == 0)
            return true;

        var distinctIds = brokerIds.Distinct().ToList();
        var existingCount = await _context.Brokers
            .CountAsync(b => distinctIds.Contains(b.Id) && b.DeletedAt == null, cancellationToken);

        return existingCount == distinctIds.Count;
    }

    private static bool BeValidTime(string? time)
    {
        if (string.IsNullOrWhiteSpace(time))
            return true;

        return TimeOnly.TryParseExact(time, "HH:mm", out _);
    }
}

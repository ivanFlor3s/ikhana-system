using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using Ikhana.Domain.Entities;
using MediatR;

namespace Ikhana.Application.Features.TaxStatuses;

public class CreateTaxStatusHandler : IRequestHandler<CreateTaxStatusCommand, TaxStatusResponse>
{
    private readonly IAppDbContext _context;

    public CreateTaxStatusHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<TaxStatusResponse> Handle(CreateTaxStatusCommand request, CancellationToken cancellationToken)
    {
        var entity = new TaxStatus
        {
            Code = request.Code,
            Name = request.Name,
            Description = request.Description,
            IsActive = request.IsActive
        };

        _context.TaxStatuses.Add(entity);
        await _context.SaveChangesAsync(cancellationToken);

        return new TaxStatusResponse
        {
            Id = entity.Id,
            Code = entity.Code,
            Name = entity.Name,
            Description = entity.Description,
            IsActive = entity.IsActive,
            CreatedAt = entity.CreatedAt,
            UpdatedAt = entity.UpdatedAt,
            DeletedAt = entity.DeletedAt
        };
    }
}

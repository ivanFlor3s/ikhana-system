using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.TaxStatuses;

public class UpdateTaxStatusHandler : IRequestHandler<UpdateTaxStatusCommand, TaxStatusResponse?>
{
    private readonly IAppDbContext _context;

    public UpdateTaxStatusHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<TaxStatusResponse?> Handle(UpdateTaxStatusCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.TaxStatuses
            .FirstOrDefaultAsync(t => t.Id == request.Id && t.DeletedAt == null, cancellationToken);

        if (entity == null)
            return null;

        if (request.Code != null)
            entity.Code = request.Code;
        if (request.Name != null)
            entity.Name = request.Name;
        if (request.Description != null)
            entity.Description = request.Description;
        if (request.IsActive.HasValue)
            entity.IsActive = request.IsActive.Value;

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

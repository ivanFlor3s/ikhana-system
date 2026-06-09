using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Features.Agreements;

public class UpdateAgreementHandler : IRequestHandler<UpdateAgreementCommand, AgreementResponse?>
{
    private readonly IAppDbContext _context;

    public UpdateAgreementHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<AgreementResponse?> Handle(UpdateAgreementCommand request, CancellationToken cancellationToken)
    {
        var entity = await _context.Agreements
            .FirstOrDefaultAsync(a => a.Id == request.Id && a.DeletedAt == null, cancellationToken);

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

        return new AgreementResponse
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

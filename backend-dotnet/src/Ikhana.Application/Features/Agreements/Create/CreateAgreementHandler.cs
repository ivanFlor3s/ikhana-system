using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using Ikhana.Domain.Entities;
using MediatR;

namespace Ikhana.Application.Features.Agreements;

public class CreateAgreementHandler : IRequestHandler<CreateAgreementCommand, AgreementResponse>
{
    private readonly IAppDbContext _context;

    public CreateAgreementHandler(IAppDbContext context)
    {
        _context = context;
    }

    public async Task<AgreementResponse> Handle(CreateAgreementCommand request, CancellationToken cancellationToken)
    {
        var entity = new Agreement
        {
            Code = request.Code,
            Name = request.Name,
            Description = request.Description,
            IsActive = request.IsActive
        };

        _context.Agreements.Add(entity);
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

using AutoMapper;
using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using Ikhana.Domain.Entities;
using MediatR;

namespace Ikhana.Application.Features.Agreements;

public class CreateAgreementHandler : IRequestHandler<CreateAgreementCommand, AgreementResponse>
{
    private readonly IAppDbContext _context;
    private readonly IMapper _mapper;

    public CreateAgreementHandler(IAppDbContext context, IMapper mapper)
    {
        _context = context;
        _mapper = mapper;
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

        return _mapper.Map<AgreementResponse>(entity);
    }
}

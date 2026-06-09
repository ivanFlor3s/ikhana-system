using AutoMapper;
using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Common.Models;
using Ikhana.Domain.Entities;
using MediatR;

namespace Ikhana.Application.Features.TaxStatuses;

public class CreateTaxStatusHandler : IRequestHandler<CreateTaxStatusCommand, TaxStatusResponse>
{
    private readonly IAppDbContext _context;
    private readonly IMapper _mapper;

    public CreateTaxStatusHandler(IAppDbContext context, IMapper mapper)
    {
        _context = context;
        _mapper = mapper;
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

        return _mapper.Map<TaxStatusResponse>(entity);
    }
}

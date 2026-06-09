using AutoMapper;
using Ikhana.Application.Common.Models;
using Ikhana.Domain.Entities;

namespace Ikhana.Application.Common.Mappings;

public class TaxStatusMappingProfile : Profile
{
    public TaxStatusMappingProfile()
    {
        CreateMap<TaxStatus, TaxStatusResponse>();

        CreateMap<TaxStatus, TaxStatusDetailResponse>()
            .ForMember(d => d.ProvidersCount, o => o.MapFrom(s => s.Providers.Count));
    }
}

using AutoMapper;
using Ikhana.Application.Common.Models;
using Ikhana.Domain.Entities;

namespace Ikhana.Application.Common.Mappings;

public class AgreementMappingProfile : Profile
{
    public AgreementMappingProfile()
    {
        CreateMap<Agreement, AgreementResponse>();

        CreateMap<Agreement, AgreementDetailResponse>()
            .ForMember(d => d.ProvidersCount, o => o.MapFrom(s => s.Providers.Count));
    }
}

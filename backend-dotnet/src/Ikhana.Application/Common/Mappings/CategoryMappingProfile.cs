using AutoMapper;
using Ikhana.Application.Common.Models;
using Ikhana.Domain.Entities;

namespace Ikhana.Application.Common.Mappings;

public class CategoryMappingProfile : Profile
{
    public CategoryMappingProfile()
    {
        CreateMap<Category, CategoryResponse>();

        CreateMap<Category, CategoryDetailResponse>()
            .ForMember(d => d.ProvidersCount, o => o.MapFrom(s => s.Providers.Count));
    }
}

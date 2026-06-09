using AutoMapper;
using Ikhana.Application.Common.Models;
using Ikhana.Domain.Entities;

namespace Ikhana.Application.Common.Mappings;

public class BrokerMappingProfile : Profile
{
    public BrokerMappingProfile()
    {
        CreateMap<Broker, BrokerResponse>();
    }
}

using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Common.Interfaces;

public interface IAppDbContext
{
    DbSet<Category> Categories { get; }
    DbSet<TaxStatus> TaxStatuses { get; }
    DbSet<Agreement> Agreements { get; }
    DbSet<Broker> Brokers { get; }
    DbSet<RawMaterialType> RawMaterialTypes { get; }
    DbSet<RawMaterialCharacteristic> RawMaterialCharacteristics { get; }
    DbSet<IramOhmMaxResistance> IramOhmMaxResistances { get; }
    DbSet<Provider> Providers { get; }
    Task<int> SaveChangesAsync(CancellationToken cancellationToken = default);
}

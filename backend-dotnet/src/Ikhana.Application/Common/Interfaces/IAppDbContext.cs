using Ikhana.Domain.Entities;
using Microsoft.EntityFrameworkCore;

namespace Ikhana.Application.Common.Interfaces;

public interface IAppDbContext
{
    DbSet<Category> Categories { get; }
    DbSet<TaxStatus> TaxStatuses { get; }
    DbSet<Agreement> Agreements { get; }
    DbSet<Broker> Brokers { get; }
    Task<int> SaveChangesAsync(CancellationToken cancellationToken = default);
}

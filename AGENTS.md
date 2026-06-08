# Ikhana — Agent Reference

Monorepo: three codebases. The active development target is `backend-dotnet/`.

| Directory | Stack | Status |
|-----------|-------|--------|
| `backend/` | Laravel 11 + MySQL (Docker) | Production — do not modify |
| `frontend/` | Angular 19 | Production — do not modify |
| `backend-dotnet/` | .NET 10 + EF Core | Active migration target |


## Architecture Conventions

### Clean Architecture layers (strict dependency order)

```
Domain → Application → Infrastructure → Api
  ↑          ↑              ↑            ↑
  Entities,  CQRS,          EF Core,     Controllers,
  Enums,     Validators,    MySql,       JWT, Serilog,
  Interfaces Interfaces     Services     Middleware
```

- **Controllers over Minimal APIs** — `AddControllers()` + `MapControllers()`, never `MapGet`/`MapPost`
- **CQRS with MediatR 14.1.0** — handlers in `Application/Features/`
- **Validation with FluentValidation 12.1.1** — validators auto-registered via `AddValidatorsFromAssembly`. No `DataAnnotations` or inline validation.
- **EF Core with Fluent API configuration** — `IEntityTypeConfiguration<T>` classes in `Infrastructure/Persistence/Configurations/`, NOT attributes on entities
- **Pomelo MySQL provider** — `UseMySql()` + `ServerVersion.AutoDetect()` in Infrastructure DI

### CRITICAL: Pomelo version constraint

**Pomelo 9.0.0 only supports EF Core 9.x (not 10.x).** All EF Core packages must stay at 9.0.16, even though the project targets `net10.0`. Upgrading any EF Core package to 10.x causes a `NU1608` error (treated as error per `TreatWarningsAsErrors`).


## Identity & Auth
- Uses ASP.NET Core Identity with long key type (IdentityUser<long>, IdentityRole<long>)
- JWT-based, stateless (no server-side token revocation). Config in appsettings.json → Jwt section.
- AddHttpContextAccessor() is required in Program.cs (audit trail depends on it)
- Response format: { success, data, message } (matches PHP backend convention)

## Soft delete + Audit trail
- ISoftDelete interface → entities with DeletedAt property. Handled manually in SaveChangesAsync, NOT via EF Core global query filter.
- IAuditable marker interface → auto-creates AuditLog rows on save. Captures old/new values as JSON, filters out timestamps.
- InternalsVisibleTo enables WebApplicationFactory<Program> in integration tests.

## Tests

Three test projects, all xUnit:

| Project | Focus | Key packages |
|---------|-------|-------------|
| `Ikhana.Domain.Tests` | Entity unit tests | FluentAssertions |
| `Ikhana.Application.Tests` | Handler/validator tests | FluentAssertions, Moq |
| `Ikhana.Api.Tests` | Integration tests | WebApplicationFactory, FluentAssertions |

- Integration tests use `WebApplicationFactory<Program>` — `Program` needs `InternalsVisibleTo` in `Ikhana.Api.csproj`
- Tests hit the real `ikhanet` MySQL database (no in-memory/SQLite)
- Use `IAsyncLifetime` for async seed data in integration tests, NOT the `IClassFixture` constructor
- TestWebApplicationFactory should be a plain stub; seed logic belongs in `IAsyncLifetime.InitializeAsync`

## Build & Run
```bash
cd backend-dotnet
# Build (0 warnings, 0 errors required)
dotnet build
# Run API (http://localhost:5089, docs at /scalar/v1)
dotnet run --project src/Ikhana.Api/
# Watch (hot reload)
dotnet watch --project src/Ikhana.Api/
# All tests
dotnet test
# Single test class
dotnet test --filter "FullyQualifiedName~AuthTests"
```

## EF Core migrations
```bash
# Add migration
dotnet ef migrations add <Name> \
  --project src/Ikhana.Infrastructure/ \
  --startup-project src/Ikhana.Api/
# Apply to database
dotnet ef database update \
  --project src/Ikhana.Infrastructure/ \
  --startup-project src/Ikhana.Api/
# Revert last migration
dotnet ef database update 0 \
  --project src/Ikhana.Infrastructure/ \
  --startup-project src/Ikhana.Api/
dotnet ef migrations remove \
  --project src/Ikhana.Infrastructure/ \
  --startup-project src/Ikhana.Api/
```
Always pass both `--project` and `--startup-project`. The `.slnx` format does not resolve default projects from solution context.

# Architectural Rules & Standards

## Application Project Structure (Vertical Slices + Clean Architecture)
- We use Feature-Driven development inside the `Features/` folder.
- DO NOT create global folders like `Services/`, `Repositories/`, or `DTOs/` for domain-specific logic.
- Each major entity group must have its own folder under `Features/` (e.g., `Features/Categories/`).

## Feature Folder Layout (Grouped by Action)
When creating or modifying a feature, group files by action sub-folders to avoid flat-file clutter:
- `Features/[FeatureName]/[Action]/`
- Inside the action folder, keep the Command/Query, Handler, and Validator together.
- Example: 
  - `Features/Categories/Create/CreateCategoryCommand.cs`
  - `Features/Categories/Create/CreateCategoryHandler.cs`
  - `Features/Categories/Create/CreateCategoryValidator.cs`

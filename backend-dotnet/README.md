# Ikhana .NET 10 API

## Prerequisites

- [.NET 10 SDK](https://dotnet.microsoft.com/download)

## Quick Start

```bash
cd backend-dotnet
dotnet run --project src/Ikhana.Api/
```

API listens on `http://localhost:5089`  
Scalar API docs at `http://localhost:5089/scalar/v1`

## CLI Commands

```bash
# Build
dotnet build

# Run
dotnet run --project src/Ikhana.Api/

# Run with hot reload (restarts on file changes)
dotnet watch --project src/Ikhana.Api/

# Run tests
dotnet test

# Format code
dotnet format

# Add a NuGet package to a project
dotnet add src/Ikhana.Api/ package <PackageName>

# Add a project reference
dotnet add src/Ikhana.Api/ reference src/Ikhana.Application/

# EF Core migrations
dotnet ef migrations add <MigrationName> --project src/Ikhana.Infrastructure/ --startup-project src/Ikhana.Api/
dotnet ef database update --project src/Ikhana.Infrastructure/ --startup-project src/Ikhana.Api/
```

## VS IDE Equivalents

| VS IDE | CLI |
|---|---|
| Build (`Ctrl+Shift+B`) | `dotnet build` |
| Run/Debug (`F5`) | `dotnet run` |
| Hot Reload | `dotnet watch` |
| Manage NuGet Packages | `dotnet add package` |
| Add Reference | `dotnet add reference` |
| Run Tests | `dotnet test` |

## Project Structure

```
backend-dotnet/
├── Ikhana.slnx
├── Directory.Build.props
├── src/
│   ├── Ikhana.Api/              # ASP.NET Core Web API (controllers, middleware)
│   ├── Ikhana.Application/      # CQRS handlers, validators, DTOs, interfaces
│   ├── Ikhana.Domain/           # Entities, enums, value objects
│   └── Ikhana.Infrastructure/   # EF Core, MySQL, external services
└── tests/
```

## Tech Stack

| Concern | Library |
|---|---|
| Framework | ASP.NET Core 10 |
| ORM | Entity Framework Core + Pomelo MySQL |
| Auth | JWT Bearer |
| Mediator | MediatR |
| Validation | FluentValidation |
| API Docs | Scalar / OpenAPI |
| Logging | Serilog |

## Configuration

Database connection string and Serilog settings in `src/Ikhana.Api/appsettings.json`.

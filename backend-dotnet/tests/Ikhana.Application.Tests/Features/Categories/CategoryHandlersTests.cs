using FluentAssertions;
using Ikhana.Application.Common.Interfaces;
using Ikhana.Application.Features.Categories;
using Ikhana.Domain.Entities;
using MediatR;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Logging;

namespace Ikhana.Application.Tests.Features.Categories;

public class CategoryHandlersTests : IAsyncLifetime
{
    private readonly IServiceProvider _services;
    private TestDbContext _db = null!;
    private ISender _sender = null!;

    public CategoryHandlersTests()
    {
        var serviceCollection = new ServiceCollection();

        var options = new DbContextOptionsBuilder<TestDbContext>()
            .UseInMemoryDatabase(Guid.NewGuid().ToString())
            .Options;
        _db = new TestDbContext(options);

        serviceCollection.AddLogging();
        serviceCollection.AddSingleton<IAppDbContext>(_db);
        serviceCollection.AddMediatR(cfg => cfg.RegisterServicesFromAssembly(typeof(CreateCategoryCommand).Assembly));
        serviceCollection.AddAutoMapper(typeof(CreateCategoryCommand).Assembly);

        _services = serviceCollection.BuildServiceProvider();
        _sender = _services.GetRequiredService<ISender>();
    }

    public async Task InitializeAsync()
    {
        _db.Categories.Add(new Category { Id = 1, Name = "Cobre", Description = "Materiales de cobre", CreatedAt = new DateTime(2024, 1, 1), UpdatedAt = new DateTime(2024, 1, 1) });
        _db.Categories.Add(new Category { Id = 2, Name = "PVC", Description = "Materiales de PVC", CreatedAt = new DateTime(2024, 6, 1), UpdatedAt = new DateTime(2024, 6, 1) });
        _db.Categories.Add(new Category { Id = 3, Name = "Acero", Description = null, CreatedAt = new DateTime(2024, 3, 1), UpdatedAt = new DateTime(2024, 3, 1) });
        await _db.SaveChangesAsync();
    }

    public Task DisposeAsync() => Task.CompletedTask;

    [Fact]
    public async Task GetCategories_returns_paginated_results_sorted_by_name_asc()
    {
        var result = await _sender.Send(new GetCategoriesQuery(1, 10, "name", "asc"));

        result.TotalCount.Should().Be(3);
        result.Items.Should().HaveCount(3);
        result.Items[0].Name.Should().Be("Acero");
        result.Items[1].Name.Should().Be("Cobre");
        result.Items[2].Name.Should().Be("PVC");
    }

    [Fact]
    public async Task GetCategories_respects_pagination()
    {
        var result = await _sender.Send(new GetCategoriesQuery(1, 2, "name", "asc"));

        result.TotalCount.Should().Be(3);
        result.Items.Should().HaveCount(2);
        result.Page.Should().Be(1);
        result.HasNextPage.Should().BeTrue();
    }

    [Fact]
    public async Task GetCategories_sorts_by_created_at()
    {
        var result = await _sender.Send(new GetCategoriesQuery(1, 10, "created_at", "asc"));

        result.Items[0].Name.Should().Be("Cobre");
        result.Items[2].Name.Should().Be("PVC");
    }

    [Fact]
    public async Task GetCategoryById_returns_detail_with_providers_count()
    {
        var result = await _sender.Send(new GetCategoryByIdQuery(1));

        result.Should().NotBeNull();
        result!.Id.Should().Be(1);
        result.Name.Should().Be("Cobre");
        result.ProvidersCount.Should().Be(0);
    }

    [Fact]
    public async Task GetCategoryById_returns_null_for_non_existent()
    {
        var result = await _sender.Send(new GetCategoryByIdQuery(999));

        result.Should().BeNull();
    }

    [Fact]
    public async Task CreateCategory_creates_and_returns_response()
    {
        var result = await _sender.Send(new CreateCategoryCommand("Nuevo", "Desc"));

        result.Should().NotBeNull();
        result.Id.Should().BeGreaterThan(0);
        result.Name.Should().Be("Nuevo");

        var saved = await _db.Categories.FindAsync(result.Id);
        saved.Should().NotBeNull();
        saved!.Name.Should().Be("Nuevo");
    }

    [Fact]
    public async Task UpdateCategory_updates_existing()
    {
        var result = await _sender.Send(new UpdateCategoryCommand(1, "Cobre Modificado", "Nueva desc"));

        result.Should().NotBeNull();
        result!.Name.Should().Be("Cobre Modificado");
        result.Description.Should().Be("Nueva desc");
    }

    [Fact]
    public async Task UpdateCategory_returns_null_for_non_existent()
    {
        var result = await _sender.Send(new UpdateCategoryCommand(999, "X", null));

        result.Should().BeNull();
    }

    [Fact]
    public async Task DeleteCategory_returns_true_for_existing()
    {
        var result = await _sender.Send(new DeleteCategoryCommand(1));

        result.Should().BeTrue();

        var deleted = await _db.Categories.FindAsync((long)1);
        deleted.Should().BeNull();
    }

    [Fact]
    public async Task DeleteCategory_returns_false_for_non_existent()
    {
        var result = await _sender.Send(new DeleteCategoryCommand(999));

        result.Should().BeFalse();
    }
}

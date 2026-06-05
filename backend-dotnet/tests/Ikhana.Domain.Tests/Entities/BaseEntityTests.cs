using FluentAssertions;
using Ikhana.Domain.Entities;

namespace Ikhana.Domain.Tests.Entities;

public class BaseEntityTests
{
    private class TestEntity : BaseEntity
    {
    }

    [Fact]
    public void New_entity_has_created_at_set()
    {
        var entity = new TestEntity();

        entity.CreatedAt.Should().BeCloseTo(DateTime.UtcNow, TimeSpan.FromSeconds(5));
    }

    [Fact]
    public void New_entity_has_default_id_zero()
    {
        var entity = new TestEntity();

        entity.Id.Should().Be(0);
    }

    [Fact]
    public void New_entity_is_not_deleted()
    {
        var entity = new TestEntity();

        entity.DeletedAt.Should().BeNull();
    }
}

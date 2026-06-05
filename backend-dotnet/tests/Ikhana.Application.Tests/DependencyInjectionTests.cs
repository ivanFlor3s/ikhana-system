using FluentAssertions;
using FluentValidation;
using Ikhana.Application;
using Ikhana.Application.Common.Interfaces;
using MediatR;
using Microsoft.Extensions.DependencyInjection;
using Moq;

namespace Ikhana.Application.Tests;

public class DependencyInjectionTests
{
    [Fact]
    public void AddApplication_registers_mediatr()
    {
        var services = new ServiceCollection();
        services.AddLogging();

        services.AddApplication();

        var mediator = services.BuildServiceProvider().GetService<IMediator>();
        mediator.Should().NotBeNull();
    }

    [Fact]
    public void AddApplication_registers_validators()
    {
        var services = new ServiceCollection();
        services.AddApplication();

        var provider = services.BuildServiceProvider();
        var validators = provider.GetServices<IValidator<FakeCommand>>();

        validators.Should().NotBeNull();
    }

    public record FakeCommand : IRequest;
}

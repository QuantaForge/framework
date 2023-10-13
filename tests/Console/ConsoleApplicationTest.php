<?php

namespace QuantaQuirk\Tests\Console;

use QuantaQuirk\Console\Application;
use QuantaQuirk\Console\Command;
use QuantaQuirk\Contracts\Events\Dispatcher;
use QuantaQuirk\Contracts\Foundation\Application as ApplicationContract;
use QuantaQuirk\Tests\Console\Fixtures\FakeCommandWithInputPrompting;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class ConsoleApplicationTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testAddSetsQuantaQuirkInstance()
    {
        $app = $this->getMockConsole(['addToParent']);
        $command = m::mock(Command::class);
        $command->shouldReceive('setQuantaQuirk')->once()->with(m::type(ApplicationContract::class));
        $app->expects($this->once())->method('addToParent')->with($this->equalTo($command))->willReturn($command);
        $result = $app->add($command);

        $this->assertEquals($command, $result);
    }

    public function testQuantaQuirkNotSetOnSymfonyCommands()
    {
        $app = $this->getMockConsole(['addToParent']);
        $command = m::mock(SymfonyCommand::class);
        $command->shouldReceive('setQuantaQuirk')->never();
        $app->expects($this->once())->method('addToParent')->with($this->equalTo($command))->willReturn($command);
        $result = $app->add($command);

        $this->assertEquals($command, $result);
    }

    public function testResolveAddsCommandViaApplicationResolution()
    {
        $app = $this->getMockConsole(['addToParent']);
        $command = m::mock(SymfonyCommand::class);
        $app->getQuantaQuirk()->shouldReceive('make')->once()->with('foo')->andReturn(m::mock(SymfonyCommand::class));
        $app->expects($this->once())->method('addToParent')->with($this->equalTo($command))->willReturn($command);
        $result = $app->resolve('foo');

        $this->assertEquals($command, $result);
    }

    public function testCallFullyStringCommandLine()
    {
        $app = new Application(
            $app = m::mock(ApplicationContract::class, ['version' => '6.0']),
            $events = m::mock(Dispatcher::class, ['dispatch' => null, 'fire' => null]),
            'testing'
        );

        $codeOfCallingArrayInput = $app->call('help', [
            '--raw' => true,
            '--format' => 'txt',
            '--no-interaction' => true,
            '--env' => 'testing',
        ]);

        $outputOfCallingArrayInput = $app->output();

        $codeOfCallingStringInput = $app->call(
            'help --raw --format=txt --no-interaction --env=testing'
        );

        $outputOfCallingStringInput = $app->output();

        $this->assertSame($codeOfCallingArrayInput, $codeOfCallingStringInput);
        $this->assertSame($outputOfCallingArrayInput, $outputOfCallingStringInput);
    }

    public function testCommandInputPromptsWhenRequiredArgumentIsMissing()
    {
        $app = new Application(
            $quantaquirk = new \QuantaQuirk\Foundation\Application(__DIR__),
            $events = m::mock(Dispatcher::class, ['dispatch' => null, 'fire' => null]),
            'testing'
        );

        $app->addCommands([$command = new FakeCommandWithInputPrompting()]);

        $command->setQuantaQuirk($quantaquirk);

        $statusCode = $app->call('fake-command-for-testing');

        $this->assertTrue($command->prompted);
        $this->assertSame(0, $statusCode);
    }

    public function testCommandInputDoesntPromptWhenRequiredArgumentIsPassed()
    {
        $app = new Application(
            $app = new \QuantaQuirk\Foundation\Application(__DIR__),
            $events = m::mock(Dispatcher::class, ['dispatch' => null, 'fire' => null]),
            'testing'
        );

        $app->addCommands([$command = new FakeCommandWithInputPrompting()]);

        $statusCode = $app->call('fake-command-for-testing', [
            'name' => 'foo',
        ]);

        $this->assertFalse($command->prompted);
        $this->assertSame(0, $statusCode);
    }

    protected function getMockConsole(array $methods)
    {
        $app = m::mock(ApplicationContract::class, ['version' => '6.0']);
        $events = m::mock(Dispatcher::class, ['dispatch' => null]);

        return $this->getMockBuilder(Application::class)->onlyMethods($methods)->setConstructorArgs([
            $app, $events, 'test-version',
        ])->getMock();
    }
}

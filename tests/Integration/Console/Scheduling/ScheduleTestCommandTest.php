<?php

namespace QuantaForge\Tests\Integration\Console\Scheduling;

use QuantaForge\Console\Application;
use QuantaForge\Console\Command;
use QuantaForge\Console\Scheduling\Schedule;
use QuantaForge\Console\Scheduling\ScheduleTestCommand;
use QuantaForge\Support\Carbon;
use Orchestra\Testbench\TestCase;

class ScheduleTestCommandTest extends TestCase
{
    public $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(now()->startOfYear());

        $this->schedule = $this->app->make(Schedule::class);
    }

    public function testRunNoDefinedCommands()
    {
        $this->artisan(ScheduleTestCommand::class)
            ->assertSuccessful()
            ->expectsOutputToContain('No scheduled commands have been defined.');
    }

    public function testRunNoMatchingCommand()
    {
        $this->schedule->command(BarCommandStub::class);

        $this->artisan(ScheduleTestCommand::class, ['--name' => 'missing:command'])
            ->assertSuccessful()
            ->expectsOutputToContain('No matching scheduled command found.');
    }

    public function testRunUsingNameOption()
    {
        $this->schedule->command(BarCommandStub::class)->name('bar-command');
        $this->schedule->job(BarJobStub::class);
        $this->schedule->call(fn () => true)->name('callback');

        $expectedOutput = windows_os()
            ? 'Running ["artisan" bar:command]'
            : "Running ['artisan' bar:command]";

        $this->artisan(ScheduleTestCommand::class, ['--name' => 'bar:command'])
            ->assertSuccessful()
            ->expectsOutputToContain($expectedOutput);

        $this->artisan(ScheduleTestCommand::class, ['--name' => BarJobStub::class])
            ->assertSuccessful()
            ->expectsOutputToContain(sprintf('Running [%s]', BarJobStub::class));

        $this->artisan(ScheduleTestCommand::class, ['--name' => 'callback'])
            ->assertSuccessful()
            ->expectsOutputToContain('Running [callback]');
    }

    public function testRunUsingChoices()
    {
        $this->schedule->command(BarCommandStub::class)->name('bar-command');
        $this->schedule->job(BarJobStub::class);
        $this->schedule->call(fn () => true)->name('callback');

        $this->artisan(ScheduleTestCommand::class)
            ->assertSuccessful()
            ->expectsChoice(
                'Which command would you like to run?',
                'callback',
                [Application::formatCommandString('bar:command'), BarJobStub::class, 'callback'],
                true
            )
            ->expectsOutputToContain('Running [callback]');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Carbon::setTestNow(null);
    }
}

class BarCommandStub extends Command
{
    protected $signature = 'bar:command';

    protected $description = 'This is the description of the command.';
}

class BarJobStub
{
    public function __invoke()
    {
        // ..
    }
}

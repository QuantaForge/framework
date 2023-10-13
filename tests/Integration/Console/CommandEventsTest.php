<?php

namespace QuantaQuirk\Tests\Integration\Console;

use QuantaQuirk\Console\Events\CommandFinished;
use QuantaQuirk\Console\Events\CommandStarting;
use QuantaQuirk\Contracts\Console\Kernel as ConsoleKernel;
use QuantaQuirk\Events\Dispatcher;
use QuantaQuirk\Filesystem\Filesystem;
use QuantaQuirk\Foundation\Testing\WithConsoleEvents;
use QuantaQuirk\Support\Facades\Event;
use QuantaQuirk\Support\Str;
use Orchestra\Testbench\Foundation\Application as Testbench;
use Orchestra\Testbench\TestCase;

class CommandEventsTest extends TestCase
{
    use WithConsoleEvents;

    /**
     * The path to the file that execution logs will be written to.
     *
     * @var string
     */
    protected $logfile;

    /**
     * The Filesystem instance for writing stubs and logs.
     *
     * @var \QuantaQuirk\Filesystem\Filesystem
     */
    protected $files;

    protected function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            $this->files = new Filesystem;
            $this->logfile = storage_path(sprintf('logs/command_events_test_%s.log', (string) Str::random()));
        });

        $this->beforeApplicationDestroyed(function () {
            $this->files->delete($this->logfile);

            unset($this->files);
            unset($this->logfile);
        });

        parent::setUp();
    }

    /**
     * @dataProvider foregroundCommandEventsProvider
     */
    public function testCommandEventsReceiveParsedInput($callback)
    {
        $this->app[ConsoleKernel::class]->registerCommand(new CommandEventsTestCommand);
        $this->app[Dispatcher::class]->listen(function (CommandStarting $event) {
            array_map(fn ($e) => $this->files->append($this->logfile, $e.PHP_EOL), [
                'CommandStarting',
                $event->input->getArgument('firstname'),
                $event->input->getArgument('lastname'),
                $event->input->getOption('occupation'),
            ]);
        });

        Event::listen(function (CommandFinished $event) {
            array_map(fn ($e) => $this->files->append($this->logfile, $e.PHP_EOL), [
                'CommandFinished',
                $event->input->getArgument('firstname'),
                $event->input->getArgument('lastname'),
                $event->input->getOption('occupation'),
            ]);
        });

        value($callback, $this);

        $this->assertLogged(
            'CommandStarting', 'taylor', 'otwell', 'coding',
            'CommandFinished', 'taylor', 'otwell', 'coding',
        );
    }

    public static function foregroundCommandEventsProvider()
    {
        yield 'Foreground with array' => [function ($testCase) {
            $testCase->artisan(CommandEventsTestCommand::class, [
                'firstname' => 'taylor',
                'lastname' => 'otwell',
                '--occupation' => 'coding',
            ]);
        }];

        yield 'Foreground with string' => [function ($testCase) {
            $testCase->artisan('command-events-test-command taylor otwell --occupation=coding');
        }];
    }

    public function testCommandEventsReceiveParsedInputFromBackground()
    {
        $quantaquirk = Testbench::create(
            basePath: static::applicationBasePath(),
            resolvingCallback: function ($app) {
                $files = new Filesystem;
                $log = fn ($msg) => $files->append($this->logfile, $msg.PHP_EOL);

                $app['events']->listen(function (CommandStarting $event) use ($log) {
                    array_map(fn ($msg) => $log($msg), [
                        'CommandStarting',
                        $event->input->getArgument('firstname'),
                        $event->input->getArgument('lastname'),
                        $event->input->getOption('occupation'),
                    ]);
                });

                $app['events']->listen(function (CommandFinished $event) use ($log) {
                    array_map(fn ($msg) => $log($msg), [
                        'CommandFinished',
                        $event->input->getArgument('firstname'),
                        $event->input->getArgument('lastname'),
                        $event->input->getOption('occupation'),
                    ]);
                });
            },
        );

        tap($quantaquirk[ConsoleKernel::class], function ($kernel) {
            $kernel->rerouteSymfonyCommandEvents();
            $kernel->registerCommand(new CommandEventsTestCommand);

            $kernel->call(CommandEventsTestCommand::class, [
                'firstname' => 'taylor',
                'lastname' => 'otwell',
                '--occupation' => 'coding',
            ]);
        });

        $this->assertLogged(
            'CommandStarting', 'taylor', 'otwell', 'coding',
            'CommandFinished', 'taylor', 'otwell', 'coding',
        );

        $quantaquirk->terminate();
    }

    protected function assertLogged(...$messages)
    {
        $log = trim($this->files->get($this->logfile));

        $this->assertEquals(implode(PHP_EOL, $messages), $log);
    }
}

class CommandEventsTestCommand extends \QuantaQuirk\Console\Command
{
    protected $signature = 'command-events-test-command {firstname} {lastname} {--occupation=cook}';

    public function handle()
    {
        // ...
    }
}

<?php

namespace QuantaQuirk\Console\Scheduling;

use QuantaQuirk\Console\Application;
use QuantaQuirk\Console\Command;
use QuantaQuirk\Console\Events\ScheduledTaskFailed;
use QuantaQuirk\Console\Events\ScheduledTaskFinished;
use QuantaQuirk\Console\Events\ScheduledTaskSkipped;
use QuantaQuirk\Console\Events\ScheduledTaskStarting;
use QuantaQuirk\Contracts\Cache\Repository as Cache;
use QuantaQuirk\Contracts\Debug\ExceptionHandler;
use QuantaQuirk\Contracts\Events\Dispatcher;
use QuantaQuirk\Support\Carbon;
use QuantaQuirk\Support\Facades\Date;
use QuantaQuirk\Support\Sleep;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(name: 'schedule:run')]
class ScheduleRunCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'schedule:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the scheduled commands';

    /**
     * The schedule instance.
     *
     * @var \QuantaQuirk\Console\Scheduling\Schedule
     */
    protected $schedule;

    /**
     * The 24 hour timestamp this scheduler command started running.
     *
     * @var \QuantaQuirk\Support\Carbon
     */
    protected $startedAt;

    /**
     * Check if any events ran.
     *
     * @var bool
     */
    protected $eventsRan = false;

    /**
     * The event dispatcher.
     *
     * @var \QuantaQuirk\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * The exception handler.
     *
     * @var \QuantaQuirk\Contracts\Debug\ExceptionHandler
     */
    protected $handler;

    /**
     * The cache store implementation.
     *
     * @var \QuantaQuirk\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * The PHP binary used by the command.
     *
     * @var string
     */
    protected $phpBinary;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->startedAt = Date::now();

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param  \QuantaQuirk\Console\Scheduling\Schedule  $schedule
     * @param  \QuantaQuirk\Contracts\Events\Dispatcher  $dispatcher
     * @param  \QuantaQuirk\Contracts\Cache\Repository  $cache
     * @param  \QuantaQuirk\Contracts\Debug\ExceptionHandler  $handler
     * @return void
     */
    public function handle(Schedule $schedule, Dispatcher $dispatcher, Cache $cache, ExceptionHandler $handler)
    {
        $this->schedule = $schedule;
        $this->dispatcher = $dispatcher;
        $this->cache = $cache;
        $this->handler = $handler;
        $this->phpBinary = Application::phpBinary();

        $this->clearInterruptSignal();

        $this->newLine();

        $events = $this->schedule->dueEvents($this->quantaquirk);

        foreach ($events as $event) {
            if (! $event->filtersPass($this->quantaquirk)) {
                $this->dispatcher->dispatch(new ScheduledTaskSkipped($event));

                continue;
            }

            if ($event->onOneServer) {
                $this->runSingleServerEvent($event);
            } else {
                $this->runEvent($event);
            }

            $this->eventsRan = true;
        }

        if ($events->contains->isRepeatable()) {
            $this->repeatEvents($events->filter->isRepeatable());
        }

        if (! $this->eventsRan) {
            $this->components->info('No scheduled commands are ready to run.');
        } else {
            $this->newLine();
        }
    }

    /**
     * Run the given single server event.
     *
     * @param  \QuantaQuirk\Console\Scheduling\Event  $event
     * @return void
     */
    protected function runSingleServerEvent($event)
    {
        if ($this->schedule->serverShouldRun($event, $this->startedAt)) {
            $this->runEvent($event);
        } else {
            $this->components->info(sprintf(
                'Skipping [%s], as command already run on another server.', $event->getSummaryForDisplay()
            ));
        }
    }

    /**
     * Run the given event.
     *
     * @param  \QuantaQuirk\Console\Scheduling\Event  $event
     * @return void
     */
    protected function runEvent($event)
    {
        $summary = $event->getSummaryForDisplay();

        $command = $event instanceof CallbackEvent
            ? $summary
            : trim(str_replace($this->phpBinary, '', $event->command));

        $description = sprintf(
            '<fg=gray>%s</> Running [%s]%s',
            Carbon::now()->format('Y-m-d H:i:s'),
            $command,
            $event->runInBackground ? ' in background' : '',
        );

        $this->components->task($description, function () use ($event) {
            $this->dispatcher->dispatch(new ScheduledTaskStarting($event));

            $start = microtime(true);

            try {
                $event->run($this->quantaquirk);

                $this->dispatcher->dispatch(new ScheduledTaskFinished(
                    $event,
                    round(microtime(true) - $start, 2)
                ));

                $this->eventsRan = true;
            } catch (Throwable $e) {
                $this->dispatcher->dispatch(new ScheduledTaskFailed($event, $e));

                $this->handler->report($e);
            }

            return $event->exitCode == 0;
        });

        if (! $event instanceof CallbackEvent) {
            $this->components->bulletList([
                $event->getSummaryForDisplay(),
            ]);
        }
    }

    /**
     * Run the given repeating events.
     *
     * @param  \QuantaQuirk\Support\Collection<\QuantaQuirk\Console\Scheduling\Event>  $events
     * @return void
     */
    protected function repeatEvents($events)
    {
        $hasEnteredMaintenanceMode = false;

        while (Date::now()->lte($this->startedAt->endOfMinute())) {
            foreach ($events as $event) {
                if ($this->shouldInterrupt()) {
                    return;
                }

                if (! $event->shouldRepeatNow()) {
                    continue;
                }

                $hasEnteredMaintenanceMode = $hasEnteredMaintenanceMode || $this->quantaquirk->isDownForMaintenance();

                if ($hasEnteredMaintenanceMode && ! $event->runsInMaintenanceMode()) {
                    continue;
                }

                if (! $event->filtersPass($this->quantaquirk)) {
                    $this->dispatcher->dispatch(new ScheduledTaskSkipped($event));

                    continue;
                }

                if ($event->onOneServer) {
                    $this->runSingleServerEvent($event);
                } else {
                    $this->runEvent($event);
                }

                $this->eventsRan = true;
            }

            Sleep::usleep(100000);
        }
    }

    /**
     * Determine if the schedule run should be interrupted.
     *
     * @return bool
     */
    protected function shouldInterrupt()
    {
        return $this->cache->get('quantaquirk:schedule:interrupt', false);
    }

    /**
     * Ensure the interrupt signal is cleared.
     *
     * @return bool
     */
    protected function clearInterruptSignal()
    {
        $this->cache->forget('quantaquirk:schedule:interrupt');
    }
}

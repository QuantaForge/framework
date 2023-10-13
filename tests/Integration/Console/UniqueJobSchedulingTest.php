<?php

namespace QuantaQuirk\Tests\Integration\Console;

use QuantaQuirk\Bus\Queueable;
use QuantaQuirk\Console\Scheduling\Schedule;
use QuantaQuirk\Contracts\Queue\ShouldBeUnique;
use QuantaQuirk\Contracts\Queue\ShouldQueue;
use QuantaQuirk\Foundation\Bus\Dispatchable;
use QuantaQuirk\Queue\InteractsWithQueue;
use QuantaQuirk\Support\Facades\Queue;
use Orchestra\Testbench\TestCase;

class UniqueJobSchedulingTest extends TestCase
{
    public function testJobsPushedToQueue()
    {
        Queue::fake();
        $this->dispatch(
            TestJob::class,
            TestJob::class,
            TestJob::class,
            TestJob::class
        );

        Queue::assertPushed(TestJob::class, 4);
    }

    public function testUniqueJobsPushedToQueue()
    {
        Queue::fake();
        $this->dispatch(
            UniqueTestJob::class,
            UniqueTestJob::class,
            UniqueTestJob::class,
            UniqueTestJob::class
        );

        Queue::assertPushed(UniqueTestJob::class, 1);
    }

    private function dispatch(...$jobs)
    {
        /** @var \QuantaQuirk\Console\Scheduling\Schedule $scheduler */
        $scheduler = $this->app->make(Schedule::class);
        foreach ($jobs as $job) {
            $scheduler->job($job)->name('')->everyMinute();
        }
        $events = $scheduler->events();
        foreach ($events as $event) {
            $event->run($this->app);
        }
    }
}

class TestJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, Dispatchable;
}

class UniqueTestJob extends TestJob implements ShouldBeUnique
{
}

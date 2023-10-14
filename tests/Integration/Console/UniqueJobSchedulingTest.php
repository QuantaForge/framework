<?php

namespace QuantaForge\Tests\Integration\Console;

use QuantaForge\Bus\Queueable;
use QuantaForge\Console\Scheduling\Schedule;
use QuantaForge\Contracts\Queue\ShouldBeUnique;
use QuantaForge\Contracts\Queue\ShouldQueue;
use QuantaForge\Foundation\Bus\Dispatchable;
use QuantaForge\Queue\InteractsWithQueue;
use QuantaForge\Support\Facades\Queue;
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
        /** @var \QuantaForge\Console\Scheduling\Schedule $scheduler */
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

<?php

namespace QuantaForge\Tests\Integration\Queue;

use Exception;
use QuantaForge\Bus\Dispatcher;
use QuantaForge\Bus\Queueable;
use QuantaForge\Contracts\Cache\Repository as Cache;
use QuantaForge\Contracts\Queue\Job;
use QuantaForge\Queue\CallQueuedHandler;
use QuantaForge\Queue\InteractsWithQueue;
use QuantaForge\Queue\Middleware\WithoutOverlapping;
use Mockery as m;
use Orchestra\Testbench\TestCase;

class WithoutOverlappingJobsTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        m::close();
    }

    public function testNonOverlappingJobsAreExecuted()
    {
        OverlappingTestJob::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $job = m::mock(Job::class);

        $job->shouldReceive('hasFailed')->andReturn(false);
        $job->shouldReceive('isReleased')->andReturn(false);
        $job->shouldReceive('isDeletedOrReleased')->andReturn(false);
        $job->shouldReceive('delete')->once();

        $instance->call($job, [
            'command' => serialize($command = new OverlappingTestJob),
        ]);

        $lockKey = (new WithoutOverlapping)->getLockKey($command);

        $this->assertTrue(OverlappingTestJob::$handled);
        $this->assertTrue($this->app->get(Cache::class)->lock($lockKey, 10)->acquire());
    }

    public function testLockIsReleasedOnJobExceptions()
    {
        FailedOverlappingTestJob::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $job = m::mock(Job::class);

        $job->shouldReceive('hasFailed')->andReturn(false);
        $job->shouldReceive('isReleased')->andReturn(false);
        $job->shouldReceive('isDeletedOrReleased')->andReturn(false);

        $this->expectException(Exception::class);

        try {
            $instance->call($job, [
                'command' => serialize($command = new FailedOverlappingTestJob),
            ]);
        } finally {
            $lockKey = (new WithoutOverlapping)->getLockKey($command);

            $this->assertTrue(FailedOverlappingTestJob::$handled);
            $this->assertTrue($this->app->get(Cache::class)->lock($lockKey, 10)->acquire());
        }
    }

    public function testOverlappingJobsAreReleased()
    {
        OverlappingTestJob::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $lockKey = (new WithoutOverlapping)->getLockKey($command = new OverlappingTestJob);
        $this->app->get(Cache::class)->lock($lockKey, 10)->acquire();

        $job = m::mock(Job::class);

        $job->shouldReceive('release')->once();
        $job->shouldReceive('hasFailed')->andReturn(false);
        $job->shouldReceive('isReleased')->andReturn(true);
        $job->shouldReceive('isDeletedOrReleased')->andReturn(true);

        $instance->call($job, [
            'command' => serialize($command),
        ]);

        $this->assertFalse(OverlappingTestJob::$handled);
    }

    public function testOverlappingJobsCanBeSkipped()
    {
        SkipOverlappingTestJob::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $lockKey = (new WithoutOverlapping)->getLockKey($command = new SkipOverlappingTestJob);
        $this->app->get(Cache::class)->lock($lockKey, 10)->acquire();

        $job = m::mock(Job::class);

        $job->shouldReceive('hasFailed')->andReturn(false);
        $job->shouldReceive('isReleased')->andReturn(false);
        $job->shouldReceive('isDeletedOrReleased')->andReturn(false);
        $job->shouldReceive('delete')->once();

        $instance->call($job, [
            'command' => serialize($command),
        ]);

        $this->assertFalse(SkipOverlappingTestJob::$handled);
    }

    public function testCanShareKeyAcrossJobs()
    {
        OverlappingTestJobWithSharedKeyOne::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $lockKey = (new WithoutOverlapping)->shared()->getLockKey(new OverlappingTestJobWithSharedKeyTwo);
        $this->app->get(Cache::class)->lock($lockKey, 10)->acquire();

        $job = m::mock(Job::class);

        $job->shouldReceive('release')->once();
        $job->shouldReceive('hasFailed')->andReturn(false);
        $job->shouldReceive('isReleased')->andReturn(true);
        $job->shouldReceive('isDeletedOrReleased')->andReturn(true);

        $instance->call($job, [
            'command' => serialize(new OverlappingTestJobWithSharedKeyOne),
        ]);

        $this->assertFalse(OverlappingTestJob::$handled);
    }

    public function testGetLock()
    {
        $job = new OverlappingTestJob;

        $this->assertSame(
            'quantaforge-queue-overlap:QuantaForge\\Tests\\Integration\\Queue\\OverlappingTestJob:key',
            (new WithoutOverlapping('key'))->getLockKey($job)
        );

        $this->assertSame(
            'quantaforge-queue-overlap:key',
            (new WithoutOverlapping('key'))->shared()->getLockKey($job)
        );

        $this->assertSame(
            'prefix:QuantaForge\\Tests\\Integration\\Queue\\OverlappingTestJob:key',
            (new WithoutOverlapping('key'))->withPrefix('prefix:')->getLockKey($job)
        );

        $this->assertSame(
            'prefix:key',
            (new WithoutOverlapping('key'))->withPrefix('prefix:')->shared()->getLockKey($job)
        );
    }
}

class OverlappingTestJob
{
    use InteractsWithQueue, Queueable;

    public static $handled = false;

    public function handle()
    {
        static::$handled = true;
    }

    public function middleware()
    {
        return [new WithoutOverlapping];
    }
}

class SkipOverlappingTestJob extends OverlappingTestJob
{
    public function middleware()
    {
        return [(new WithoutOverlapping)->dontRelease()];
    }
}

class FailedOverlappingTestJob extends OverlappingTestJob
{
    public function handle()
    {
        static::$handled = true;

        throw new Exception;
    }
}

class OverlappingTestJobWithSharedKeyOne
{
    use InteractsWithQueue, Queueable;

    public static $handled = false;

    public function handle()
    {
        static::$handled = true;
    }

    public function middleware()
    {
        return [(new WithoutOverlapping)->shared()];
    }
}

class OverlappingTestJobWithSharedKeyTwo
{
    use InteractsWithQueue, Queueable;

    public static $handled = false;

    public function handle()
    {
        static::$handled = true;
    }

    public function middleware()
    {
        return [(new WithoutOverlapping)->shared()];
    }
}

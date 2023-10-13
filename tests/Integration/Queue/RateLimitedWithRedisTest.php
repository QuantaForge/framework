<?php

namespace QuantaQuirk\Tests\Integration\Queue;

use QuantaQuirk\Bus\Dispatcher;
use QuantaQuirk\Bus\Queueable;
use QuantaQuirk\Cache\RateLimiter;
use QuantaQuirk\Cache\RateLimiting\Limit;
use QuantaQuirk\Contracts\Queue\Job;
use QuantaQuirk\Contracts\Redis\Factory as Redis;
use QuantaQuirk\Foundation\Testing\Concerns\InteractsWithRedis;
use QuantaQuirk\Queue\CallQueuedHandler;
use QuantaQuirk\Queue\InteractsWithQueue;
use QuantaQuirk\Queue\Middleware\RateLimitedWithRedis;
use QuantaQuirk\Support\Str;
use Mockery as m;
use Orchestra\Testbench\TestCase;

class RateLimitedWithRedisTest extends TestCase
{
    use InteractsWithRedis;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpRedis();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->tearDownRedis();

        m::close();
    }

    public function testUnlimitedJobsAreExecuted()
    {
        $rateLimiter = $this->app->make(RateLimiter::class);

        $testJob = new RedisRateLimitedTestJob;

        $rateLimiter->for($testJob->key, function ($job) {
            return Limit::none();
        });

        $this->assertJobRanSuccessfully($testJob);
        $this->assertJobRanSuccessfully($testJob);
    }

    public function testRateLimitedJobsAreNotExecutedOnLimitReached()
    {
        $rateLimiter = $this->app->make(RateLimiter::class);

        $testJob = new RedisRateLimitedTestJob;

        $rateLimiter->for($testJob->key, function ($job) {
            return Limit::perMinute(1);
        });

        $this->assertJobRanSuccessfully($testJob);
        $this->assertJobWasReleased($testJob);
    }

    public function testRateLimitedJobsCanBeSkippedOnLimitReached()
    {
        $rateLimiter = $this->app->make(RateLimiter::class);

        $testJob = new RedisRateLimitedDontReleaseTestJob;

        $rateLimiter->for($testJob->key, function ($job) {
            return Limit::perMinute(1);
        });

        $this->assertJobRanSuccessfully($testJob);
        $this->assertJobWasSkipped($testJob);
    }

    public function testJobsCanHaveConditionalRateLimits()
    {
        $rateLimiter = $this->app->make(RateLimiter::class);

        $adminJob = new RedisAdminTestJob;

        $rateLimiter->for($adminJob->key, function ($job) {
            if ($job->isAdmin()) {
                return Limit::none();
            }

            return Limit::perMinute(1);
        });

        $this->assertJobRanSuccessfully($adminJob);
        $this->assertJobRanSuccessfully($adminJob);

        $nonAdminJob = new RedisNonAdminTestJob;

        $rateLimiter->for($nonAdminJob->key, function ($job) {
            if ($job->isAdmin()) {
                return Limit::none();
            }

            return Limit::perMinute(1);
        });

        $this->assertJobRanSuccessfully($nonAdminJob);
        $this->assertJobWasReleased($nonAdminJob);
    }

    public function testMiddlewareSerialization()
    {
        $rateLimited = new RateLimitedWithRedis('limiterName');
        $rateLimited->shouldRelease = false;

        $restoredRateLimited = unserialize(serialize($rateLimited));

        $fetch = (function (string $name) {
            return $this->{$name};
        })->bindTo($restoredRateLimited, RateLimitedWithRedis::class);

        $this->assertFalse($restoredRateLimited->shouldRelease);
        $this->assertSame('limiterName', $fetch('limiterName'));
        $this->assertInstanceOf(RateLimiter::class, $fetch('limiter'));
        $this->assertInstanceOf(Redis::class, $fetch('redis'));
    }

    protected function assertJobRanSuccessfully($testJob)
    {
        $testJob::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $job = m::mock(Job::class);

        $job->shouldReceive('hasFailed')->once()->andReturn(false);
        $job->shouldReceive('isReleased')->andReturn(false);
        $job->shouldReceive('isDeletedOrReleased')->once()->andReturn(false);
        $job->shouldReceive('delete')->once();

        $instance->call($job, [
            'command' => serialize($testJob),
        ]);

        $this->assertTrue($testJob::$handled);
    }

    protected function assertJobWasReleased($testJob)
    {
        $testJob::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $job = m::mock(Job::class);

        $job->shouldReceive('hasFailed')->once()->andReturn(false);
        $job->shouldReceive('release')->once();
        $job->shouldReceive('isReleased')->andReturn(true);
        $job->shouldReceive('isDeletedOrReleased')->once()->andReturn(true);

        $instance->call($job, [
            'command' => serialize($testJob),
        ]);

        $this->assertFalse($testJob::$handled);
    }

    protected function assertJobWasSkipped($testJob)
    {
        $testJob::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $job = m::mock(Job::class);

        $job->shouldReceive('hasFailed')->once()->andReturn(false);
        $job->shouldReceive('isReleased')->andReturn(false);
        $job->shouldReceive('isDeletedOrReleased')->once()->andReturn(false);
        $job->shouldReceive('delete')->once();

        $instance->call($job, [
            'command' => serialize($testJob),
        ]);

        $this->assertFalse($testJob::$handled);
    }
}

class RedisRateLimitedTestJob
{
    use InteractsWithQueue, Queueable;

    public $key;

    public static $handled = false;

    public function __construct()
    {
        $this->key = Str::random(10);
    }

    public function handle()
    {
        static::$handled = true;
    }

    public function middleware()
    {
        return [new RateLimitedWithRedis($this->key)];
    }
}

class RedisAdminTestJob extends RedisRateLimitedTestJob
{
    public function isAdmin()
    {
        return true;
    }
}

class RedisNonAdminTestJob extends RedisRateLimitedTestJob
{
    public function isAdmin()
    {
        return false;
    }
}

class RedisRateLimitedDontReleaseTestJob extends RedisRateLimitedTestJob
{
    public function middleware()
    {
        return [(new RateLimitedWithRedis($this->key))->dontRelease()];
    }
}
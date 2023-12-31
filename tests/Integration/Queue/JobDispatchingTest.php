<?php

namespace QuantaForge\Tests\Integration\Queue;

use QuantaForge\Bus\Queueable;
use QuantaForge\Contracts\Cache\Repository;
use QuantaForge\Contracts\Queue\ShouldBeUnique;
use QuantaForge\Contracts\Queue\ShouldQueue;
use QuantaForge\Foundation\Bus\Dispatchable;
use QuantaForge\Queue\InteractsWithQueue;
use Orchestra\Testbench\TestCase;

class JobDispatchingTest extends TestCase
{
    protected function tearDown(): void
    {
        Job::$ran = false;
        Job::$value = null;
    }

    public function testJobCanUseCustomMethodsAfterDispatch()
    {
        Job::dispatch('test')->replaceValue('new-test');

        $this->assertTrue(Job::$ran);
        $this->assertSame('new-test', Job::$value);
    }

    public function testDispatchesConditionallyWithBoolean()
    {
        Job::dispatchIf(false, 'test')->replaceValue('new-test');

        $this->assertFalse(Job::$ran);
        $this->assertNull(Job::$value);

        Job::dispatchIf(true, 'test')->replaceValue('new-test');

        $this->assertTrue(Job::$ran);
        $this->assertSame('new-test', Job::$value);
    }

    public function testDispatchesConditionallyWithClosure()
    {
        Job::dispatchIf(fn ($job) => $job instanceof Job ? 0 : 1, 'test')->replaceValue('new-test');

        $this->assertFalse(Job::$ran);

        Job::dispatchIf(fn ($job) => $job instanceof Job ? 1 : 0, 'test')->replaceValue('new-test');

        $this->assertTrue(Job::$ran);
    }

    public function testDoesNotDispatchConditionallyWithBoolean()
    {
        Job::dispatchUnless(true, 'test')->replaceValue('new-test');

        $this->assertFalse(Job::$ran);
        $this->assertNull(Job::$value);

        Job::dispatchUnless(false, 'test')->replaceValue('new-test');

        $this->assertTrue(Job::$ran);
        $this->assertSame('new-test', Job::$value);
    }

    public function testDoesNotDispatchConditionallyWithClosure()
    {
        Job::dispatchUnless(fn ($job) => $job instanceof Job ? 1 : 0, 'test')->replaceValue('new-test');

        $this->assertFalse(Job::$ran);

        Job::dispatchUnless(fn ($job) => $job instanceof Job ? 0 : 1, 'test')->replaceValue('new-test');

        $this->assertTrue(Job::$ran);
    }

    public function testUniqueJobLockIsReleasedForJobDispatchedAfterResponse()
    {
        // get initial terminatingCallbacks
        $terminatingCallbacksReflectionProperty = (new \ReflectionObject($this->app))->getProperty('terminatingCallbacks');
        $startTerminatingCallbacks = $terminatingCallbacksReflectionProperty->getValue($this->app);

        UniqueJob::dispatchAfterResponse('test');
        $this->assertFalse(
            $this->getJobLock(UniqueJob::class, 'test')
        );

        $this->app->terminate();
        $this->assertTrue(UniqueJob::$ran);

        $terminatingCallbacksReflectionProperty->setValue($this->app, $startTerminatingCallbacks);

        UniqueJob::$ran = false;
        UniqueJob::dispatch('test')->afterResponse();
        $this->app->terminate();
        $this->assertTrue(UniqueJob::$ran);

        // acquire job lock and confirm that job is not dispatched after response
        $this->assertTrue(
            $this->getJobLock(UniqueJob::class, 'test')
        );
        $terminatingCallbacksReflectionProperty->setValue($this->app, $startTerminatingCallbacks);
        UniqueJob::$ran = false;
        UniqueJob::dispatch('test')->afterResponse();
        $this->app->terminate();
        $this->assertFalse(UniqueJob::$ran);

        // confirm that dispatchAfterResponse also does not run
        UniqueJob::dispatchAfterResponse('test');
        $this->app->terminate();
        $this->assertFalse(UniqueJob::$ran);
    }

    /**
     * Helpers.
     */
    private function getJobLock($job, $value = null)
    {
        return $this->app->get(Repository::class)->lock('quantaforge_unique_job:'.$job.$value, 10)->get();
    }
}

class Job implements ShouldQueue
{
    use Dispatchable, Queueable;

    public static $ran = false;
    public static $usedQueue = null;
    public static $usedConnection = null;
    public static $value = null;

    public function __construct($value)
    {
        static::$value = $value;
    }

    public function handle()
    {
        static::$ran = true;
    }

    public function replaceValue($value)
    {
        static::$value = $value;
    }
}

class UniqueJob extends Job implements ShouldBeUnique
{
    use InteractsWithQueue;

    public function uniqueId()
    {
        return self::$value;
    }
}

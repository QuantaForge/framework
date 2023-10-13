<?php

namespace QuantaQuirk\Tests\Console\Scheduling;

use QuantaQuirk\Console\Scheduling\CacheEventMutex;
use QuantaQuirk\Console\Scheduling\CacheSchedulingMutex;
use QuantaQuirk\Console\Scheduling\Event;
use QuantaQuirk\Contracts\Cache\Factory;
use QuantaQuirk\Contracts\Cache\Repository;
use QuantaQuirk\Support\Carbon;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class CacheSchedulingMutexTest extends TestCase
{
    /**
     * @var \QuantaQuirk\Console\Scheduling\CacheSchedulingMutex
     */
    protected $cacheMutex;

    /**
     * @var \QuantaQuirk\Console\Scheduling\Event
     */
    protected $event;

    /**
     * @var \QuantaQuirk\Support\Carbon
     */
    protected $time;

    /**
     * @var \QuantaQuirk\Contracts\Cache\Factory
     */
    protected $cacheFactory;

    /**
     * @var \QuantaQuirk\Contracts\Cache\Repository
     */
    protected $cacheRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheFactory = m::mock(Factory::class);
        $this->cacheRepository = m::mock(Repository::class);
        $this->cacheFactory->shouldReceive('store')->andReturn($this->cacheRepository);
        $this->cacheMutex = new CacheSchedulingMutex($this->cacheFactory);
        $this->event = new Event(new CacheEventMutex($this->cacheFactory), 'command');
        $this->time = Carbon::now();
    }

    public function testMutexReceivesCorrectCreate()
    {
        $this->cacheRepository->shouldReceive('add')->once()->with($this->event->mutexName().$this->time->format('Hi'), true, 3600)->andReturn(true);

        $this->assertTrue($this->cacheMutex->create($this->event, $this->time));
    }

    public function testCanUseCustomConnection()
    {
        $this->cacheFactory->shouldReceive('store')->with('test')->andReturn($this->cacheRepository);
        $this->cacheRepository->shouldReceive('add')->once()->with($this->event->mutexName().$this->time->format('Hi'), true, 3600)->andReturn(true);
        $this->cacheMutex->useStore('test');

        $this->assertTrue($this->cacheMutex->create($this->event, $this->time));
    }

    public function testPreventsMultipleRuns()
    {
        $this->cacheRepository->shouldReceive('add')->once()->with($this->event->mutexName().$this->time->format('Hi'), true, 3600)->andReturn(false);

        $this->assertFalse($this->cacheMutex->create($this->event, $this->time));
    }

    public function testChecksForNonRunSchedule()
    {
        $this->cacheRepository->shouldReceive('has')->once()->with($this->event->mutexName().$this->time->format('Hi'))->andReturn(false);

        $this->assertFalse($this->cacheMutex->exists($this->event, $this->time));
    }

    public function testChecksForAlreadyRunSchedule()
    {
        $this->cacheRepository->shouldReceive('has')->with($this->event->mutexName().$this->time->format('Hi'))->andReturn(true);

        $this->assertTrue($this->cacheMutex->exists($this->event, $this->time));
    }
}

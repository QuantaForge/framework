<?php

namespace QuantaQuirk\Tests\Support;

use QuantaQuirk\Cache\CacheManager;
use QuantaQuirk\Cache\Events\CacheMissed;
use QuantaQuirk\Config\Repository as ConfigRepository;
use QuantaQuirk\Container\Container;
use QuantaQuirk\Contracts\Events\Dispatcher as DispatcherContract;
use QuantaQuirk\Database\Eloquent\Model;
use QuantaQuirk\Events\Dispatcher;
use QuantaQuirk\Support\Facades\Cache;
use QuantaQuirk\Support\Facades\Event;
use QuantaQuirk\Support\Facades\Facade;
use QuantaQuirk\Support\Testing\Fakes\EventFake;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class SupportFacadesEventTest extends TestCase
{
    private $events;

    protected function setUp(): void
    {
        parent::setUp();

        $this->events = m::mock(Dispatcher::class);

        $container = new Container;
        $container->instance('events', $this->events);
        $container->alias('events', DispatcherContract::class);
        $container->instance('cache', new CacheManager($container));
        $container->instance('config', new ConfigRepository($this->getCacheConfig()));

        Facade::setFacadeApplication($container);
    }

    protected function tearDown(): void
    {
        Event::clearResolvedInstances();
        Event::setFacadeApplication(null);

        m::close();
    }

    public function testFakeFor()
    {
        Event::fakeFor(function () {
            (new FakeForStub)->dispatch();

            Event::assertDispatched(EventStub::class);
        });

        $this->events->shouldReceive('dispatch')->once();

        (new FakeForStub)->dispatch();
    }

    public function testFakeForSwapsDispatchers()
    {
        $arrayRepository = Cache::store('array');

        Event::fakeFor(function () use ($arrayRepository) {
            $this->assertInstanceOf(EventFake::class, Event::getFacadeRoot());
            $this->assertInstanceOf(EventFake::class, Model::getEventDispatcher());
            $this->assertInstanceOf(EventFake::class, $arrayRepository->getEventDispatcher());
        });

        $this->assertSame($this->events, Event::getFacadeRoot());
        $this->assertSame($this->events, Model::getEventDispatcher());
        $this->assertSame($this->events, $arrayRepository->getEventDispatcher());
    }

    public function testFakeSwapsDispatchersInResolvedCacheRepositories()
    {
        $arrayRepository = Cache::store('array');

        $this->events->shouldReceive('dispatch')->once();
        $arrayRepository->get('foo');

        Event::fake();

        $arrayRepository->get('bar');

        Event::assertDispatched(CacheMissed::class);
    }

    protected function getCacheConfig()
    {
        return [
            'cache' => [
                'stores' => [
                    'array' => [
                        'driver' => 'array',
                    ],
                ],
            ],
        ];
    }
}

class FakeForStub
{
    public function dispatch()
    {
        Event::dispatch(EventStub::class);
    }
}

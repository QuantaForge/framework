<?php

namespace QuantaForge\Tests\Integration\Broadcasting;

use QuantaForge\Broadcasting\BroadcastEvent;
use QuantaForge\Broadcasting\BroadcastManager;
use QuantaForge\Broadcasting\UniqueBroadcastEvent;
use QuantaForge\Config\Repository;
use QuantaForge\Container\Container;
use QuantaForge\Contracts\Broadcasting\ShouldBeUnique;
use QuantaForge\Contracts\Broadcasting\ShouldBroadcast;
use QuantaForge\Contracts\Broadcasting\ShouldBroadcastNow;
use QuantaForge\Contracts\Cache\Repository as Cache;
use QuantaForge\Support\Facades\Broadcast;
use QuantaForge\Support\Facades\Bus;
use QuantaForge\Support\Facades\Queue;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;

class BroadcastManagerTest extends TestCase
{
    public function testEventCanBeBroadcastNow()
    {
        Bus::fake();
        Queue::fake();

        Broadcast::queue(new TestEventNow);

        Bus::assertDispatched(BroadcastEvent::class);
        Queue::assertNotPushed(BroadcastEvent::class);
    }

    public function testEventsCanBeBroadcast()
    {
        Bus::fake();
        Queue::fake();

        Broadcast::queue(new TestEvent);

        Bus::assertNotDispatched(BroadcastEvent::class);
        Queue::assertPushed(BroadcastEvent::class);
    }

    public function testUniqueEventsCanBeBroadcast()
    {
        Bus::fake();
        Queue::fake();

        Broadcast::queue(new TestEventUnique);

        Bus::assertNotDispatched(UniqueBroadcastEvent::class);
        Queue::assertPushed(UniqueBroadcastEvent::class);

        $lockKey = 'quantaforge_unique_job:'.UniqueBroadcastEvent::class.TestEventUnique::class;
        $this->assertFalse($this->app->get(Cache::class)->lock($lockKey, 10)->get());
    }

    public function testThrowExceptionWhenUnknownStoreIsUsed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Broadcast connection [alien_connection] is not defined.');

        $userConfig = [
            'broadcasting' => [
                'connections' => [
                    'my_connection' => [
                        'driver' => 'pusher',
                    ],
                ],
            ],
        ];

        $app = $this->getApp($userConfig);

        $broadcastManager = new BroadcastManager($app);

        $broadcastManager->connection('alien_connection');
    }

    protected function getApp(array $userConfig)
    {
        $app = new Container;
        $app->singleton('config', fn () => new Repository($userConfig));

        return $app;
    }
}

class TestEvent implements ShouldBroadcast
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \QuantaForge\Broadcasting\Channel|\QuantaForge\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        //
    }
}

class TestEventNow implements ShouldBroadcastNow
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \QuantaForge\Broadcasting\Channel|\QuantaForge\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        //
    }
}

class TestEventUnique implements ShouldBroadcast, ShouldBeUnique
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \QuantaForge\Broadcasting\Channel|\QuantaForge\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        //
    }
}

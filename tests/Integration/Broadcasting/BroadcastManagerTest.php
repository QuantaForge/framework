<?php

namespace QuantaQuirk\Tests\Integration\Broadcasting;

use QuantaQuirk\Broadcasting\BroadcastEvent;
use QuantaQuirk\Broadcasting\BroadcastManager;
use QuantaQuirk\Broadcasting\UniqueBroadcastEvent;
use QuantaQuirk\Config\Repository;
use QuantaQuirk\Container\Container;
use QuantaQuirk\Contracts\Broadcasting\ShouldBeUnique;
use QuantaQuirk\Contracts\Broadcasting\ShouldBroadcast;
use QuantaQuirk\Contracts\Broadcasting\ShouldBroadcastNow;
use QuantaQuirk\Contracts\Cache\Repository as Cache;
use QuantaQuirk\Support\Facades\Broadcast;
use QuantaQuirk\Support\Facades\Bus;
use QuantaQuirk\Support\Facades\Queue;
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

        $lockKey = 'quantaquirk_unique_job:'.UniqueBroadcastEvent::class.TestEventUnique::class;
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
     * @return \QuantaQuirk\Broadcasting\Channel|\QuantaQuirk\Broadcasting\Channel[]
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
     * @return \QuantaQuirk\Broadcasting\Channel|\QuantaQuirk\Broadcasting\Channel[]
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
     * @return \QuantaQuirk\Broadcasting\Channel|\QuantaQuirk\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        //
    }
}

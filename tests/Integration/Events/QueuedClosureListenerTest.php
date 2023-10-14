<?php

namespace QuantaForge\Tests\Integration\Events;

use QuantaForge\Events\CallQueuedListener;
use QuantaForge\Events\InvokeQueuedClosure;
use QuantaForge\Support\Facades\Bus;
use QuantaForge\Support\Facades\Event;
use Orchestra\Testbench\TestCase;

class QueuedClosureListenerTest extends TestCase
{
    public function testAnonymousQueuedListenerIsQueued()
    {
        Bus::fake();

        Event::listen(\QuantaForge\Events\queueable(function (TestEvent $event) {
            //
        })->catch(function (TestEvent $event) {
            //
        })->onConnection(null)->onQueue(null));

        Event::dispatch(new TestEvent);

        Bus::assertDispatched(CallQueuedListener::class, function ($job) {
            return $job->class == InvokeQueuedClosure::class;
        });
    }
}

class TestEvent
{
    //
}

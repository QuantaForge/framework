<?php

namespace QuantaQuirk\Tests\Integration\Events;

use QuantaQuirk\Events\CallQueuedListener;
use QuantaQuirk\Events\InvokeQueuedClosure;
use QuantaQuirk\Support\Facades\Bus;
use QuantaQuirk\Support\Facades\Event;
use Orchestra\Testbench\TestCase;

class QueuedClosureListenerTest extends TestCase
{
    public function testAnonymousQueuedListenerIsQueued()
    {
        Bus::fake();

        Event::listen(\QuantaQuirk\Events\queueable(function (TestEvent $event) {
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

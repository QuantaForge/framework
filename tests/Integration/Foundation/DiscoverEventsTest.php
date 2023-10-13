<?php

namespace QuantaQuirk\Tests\Integration\Foundation;

use QuantaQuirk\Foundation\Events\DiscoverEvents;
use QuantaQuirk\Support\Str;
use QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventOne;
use QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventTwo;
use QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners\AbstractListener;
use QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners\Listener;
use QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners\ListenerInterface;
use QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\UnionListeners\UnionListener;
use Orchestra\Testbench\TestCase;
use SplFileInfo;

class DiscoverEventsTest extends TestCase
{
    protected function tearDown(): void
    {
        DiscoverEvents::$guessClassNamesUsingCallback = null;

        parent::tearDown();
    }

    public function testEventsCanBeDiscovered()
    {
        class_alias(Listener::class, 'Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners\Listener');
        class_alias(AbstractListener::class, 'Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners\AbstractListener');
        class_alias(ListenerInterface::class, 'Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners\ListenerInterface');

        $events = DiscoverEvents::within(__DIR__.'/Fixtures/EventDiscovery/Listeners', getcwd());

        $this->assertEquals([
            EventOne::class => [
                Listener::class.'@handle',
                Listener::class.'@handleEventOne',
            ],
            EventTwo::class => [
                Listener::class.'@handleEventTwo',
            ],
        ], $events);
    }

    public function testUnionEventsCanBeDiscovered()
    {
        class_alias(UnionListener::class, 'Tests\Integration\Foundation\Fixtures\EventDiscovery\UnionListeners\UnionListener');

        $events = DiscoverEvents::within(__DIR__.'/Fixtures/EventDiscovery/UnionListeners', getcwd());

        $this->assertEquals([
            EventOne::class => [
                UnionListener::class.'@handle',
            ],
            EventTwo::class => [
                UnionListener::class.'@handle',
            ],
        ], $events);
    }

    public function testEventsCanBeDiscoveredUsingCustomClassNameGuessing()
    {
        DiscoverEvents::guessClassNamesUsing(function (SplFileInfo $file, $basePath) {
            return Str::of($file->getRealPath())
                ->after($basePath.DIRECTORY_SEPARATOR)
                ->before('.php')
                ->replace(DIRECTORY_SEPARATOR, '\\')
                ->ucfirst()
                ->prepend('QuantaQuirk\\')
                ->toString();
        });

        $events = DiscoverEvents::within(__DIR__.'/Fixtures/EventDiscovery/Listeners', getcwd());

        $this->assertEquals([
            EventOne::class => [
                Listener::class.'@handle',
                Listener::class.'@handleEventOne',
            ],
            EventTwo::class => [
                Listener::class.'@handleEventTwo',
            ],
        ], $events);
    }
}

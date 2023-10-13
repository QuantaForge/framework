<?php

namespace QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners;

use QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventOne;

interface ListenerInterface
{
    public function handle(EventOne $event);
}

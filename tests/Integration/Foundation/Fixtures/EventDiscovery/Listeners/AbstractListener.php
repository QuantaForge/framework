<?php

namespace QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners;

use QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventOne;

abstract class AbstractListener
{
    abstract public function handle(EventOne $event);
}

<?php

namespace QuantaForge\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners;

use QuantaForge\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventOne;

abstract class AbstractListener
{
    abstract public function handle(EventOne $event);
}

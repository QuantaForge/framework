<?php

namespace QuantaForge\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners;

use QuantaForge\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventOne;

interface ListenerInterface
{
    public function handle(EventOne $event);
}

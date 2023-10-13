<?php

namespace QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners;

use QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventOne;
use QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventTwo;

class Listener
{
    public function handle(EventOne $event)
    {
        //
    }

    public function handleEventOne(EventOne $event)
    {
        //
    }

    public function handleEventTwo(EventTwo $event)
    {
        //
    }
}

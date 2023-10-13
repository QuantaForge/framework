<?php

namespace QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\UnionListeners;

use QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventOne;
use QuantaQuirk\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventTwo;

class UnionListener
{
    public function handle(EventOne|EventTwo $event)
    {
        //
    }
}

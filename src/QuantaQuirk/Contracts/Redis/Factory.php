<?php

namespace QuantaQuirk\Contracts\Redis;

interface Factory
{
    /**
     * Get a Redis connection by name.
     *
     * @param  string|null  $name
     * @return \QuantaQuirk\Redis\Connections\Connection
     */
    public function connection($name = null);
}

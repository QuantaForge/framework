<?php

namespace QuantaForge\Contracts\Redis;

interface Factory
{
    /**
     * Get a Redis connection by name.
     *
     * @param  string|null  $name
     * @return \QuantaForge\Redis\Connections\Connection
     */
    public function connection($name = null);
}

<?php

namespace QuantaQuirk\Contracts\Queue;

interface Factory
{
    /**
     * Resolve a queue connection instance.
     *
     * @param  string|null  $name
     * @return \QuantaQuirk\Contracts\Queue\Queue
     */
    public function connection($name = null);
}

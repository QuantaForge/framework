<?php

namespace QuantaForge\Contracts\Queue;

interface Factory
{
    /**
     * Resolve a queue connection instance.
     *
     * @param  string|null  $name
     * @return \QuantaForge\Contracts\Queue\Queue
     */
    public function connection($name = null);
}

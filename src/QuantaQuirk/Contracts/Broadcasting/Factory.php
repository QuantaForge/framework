<?php

namespace QuantaQuirk\Contracts\Broadcasting;

interface Factory
{
    /**
     * Get a broadcaster implementation by name.
     *
     * @param  string|null  $name
     * @return \QuantaQuirk\Contracts\Broadcasting\Broadcaster
     */
    public function connection($name = null);
}

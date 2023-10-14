<?php

namespace QuantaForge\Contracts\Broadcasting;

interface Factory
{
    /**
     * Get a broadcaster implementation by name.
     *
     * @param  string|null  $name
     * @return \QuantaForge\Contracts\Broadcasting\Broadcaster
     */
    public function connection($name = null);
}

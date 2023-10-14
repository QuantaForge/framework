<?php

namespace QuantaForge\Contracts\Cache;

interface Factory
{
    /**
     * Get a cache store instance by name.
     *
     * @param  string|null  $name
     * @return \QuantaForge\Contracts\Cache\Repository
     */
    public function store($name = null);
}

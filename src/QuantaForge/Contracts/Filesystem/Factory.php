<?php

namespace QuantaForge\Contracts\Filesystem;

interface Factory
{
    /**
     * Get a filesystem implementation.
     *
     * @param  string|null  $name
     * @return \QuantaForge\Contracts\Filesystem\Filesystem
     */
    public function disk($name = null);
}

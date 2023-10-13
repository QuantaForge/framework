<?php

namespace QuantaQuirk\Contracts\Filesystem;

interface Factory
{
    /**
     * Get a filesystem implementation.
     *
     * @param  string|null  $name
     * @return \QuantaQuirk\Contracts\Filesystem\Filesystem
     */
    public function disk($name = null);
}

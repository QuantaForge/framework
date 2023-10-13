<?php

namespace QuantaQuirk\Console\Events;

class ArtisanStarting
{
    /**
     * The Artisan application instance.
     *
     * @var \QuantaQuirk\Console\Application
     */
    public $artisan;

    /**
     * Create a new event instance.
     *
     * @param  \QuantaQuirk\Console\Application  $artisan
     * @return void
     */
    public function __construct($artisan)
    {
        $this->artisan = $artisan;
    }
}

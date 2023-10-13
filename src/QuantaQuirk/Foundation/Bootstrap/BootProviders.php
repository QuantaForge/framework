<?php

namespace QuantaQuirk\Foundation\Bootstrap;

use QuantaQuirk\Contracts\Foundation\Application;

class BootProviders
{
    /**
     * Bootstrap the given application.
     *
     * @param  \QuantaQuirk\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $app->boot();
    }
}

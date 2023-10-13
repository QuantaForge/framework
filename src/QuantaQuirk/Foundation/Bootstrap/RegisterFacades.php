<?php

namespace QuantaQuirk\Foundation\Bootstrap;

use QuantaQuirk\Contracts\Foundation\Application;
use QuantaQuirk\Foundation\AliasLoader;
use QuantaQuirk\Foundation\PackageManifest;
use QuantaQuirk\Support\Facades\Facade;

class RegisterFacades
{
    /**
     * Bootstrap the given application.
     *
     * @param  \QuantaQuirk\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        Facade::clearResolvedInstances();

        Facade::setFacadeApplication($app);

        AliasLoader::getInstance(array_merge(
            $app->make('config')->get('app.aliases', []),
            $app->make(PackageManifest::class)->aliases()
        ))->register();
    }
}

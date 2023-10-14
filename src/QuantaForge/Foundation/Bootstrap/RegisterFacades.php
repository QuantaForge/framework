<?php

namespace QuantaForge\Foundation\Bootstrap;

use QuantaForge\Contracts\Foundation\Application;
use QuantaForge\Foundation\AliasLoader;
use QuantaForge\Foundation\PackageManifest;
use QuantaForge\Support\Facades\Facade;

class RegisterFacades
{
    /**
     * Bootstrap the given application.
     *
     * @param  \QuantaForge\Contracts\Foundation\Application  $app
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

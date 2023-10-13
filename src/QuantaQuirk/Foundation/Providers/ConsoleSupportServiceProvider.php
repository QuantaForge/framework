<?php

namespace QuantaQuirk\Foundation\Providers;

use QuantaQuirk\Contracts\Support\DeferrableProvider;
use QuantaQuirk\Database\MigrationServiceProvider;
use QuantaQuirk\Support\AggregateServiceProvider;

class ConsoleSupportServiceProvider extends AggregateServiceProvider implements DeferrableProvider
{
    /**
     * The provider class names.
     *
     * @var string[]
     */
    protected $providers = [
        ArtisanServiceProvider::class,
        MigrationServiceProvider::class,
        ComposerServiceProvider::class,
    ];
}

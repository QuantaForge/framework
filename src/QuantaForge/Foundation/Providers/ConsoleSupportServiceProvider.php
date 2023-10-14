<?php

namespace QuantaForge\Foundation\Providers;

use QuantaForge\Contracts\Support\DeferrableProvider;
use QuantaForge\Database\MigrationServiceProvider;
use QuantaForge\Support\AggregateServiceProvider;

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

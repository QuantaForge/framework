<?php

namespace QuantaQuirk\Tests\Integration\Foundation\Fixtures\Providers;

use QuantaQuirk\Console\Application;
use QuantaQuirk\Support\ServiceProvider;
use QuantaQuirk\Tests\Integration\Foundation\Fixtures\Console\ThrowExceptionCommand;

class ThrowExceptionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Application::starting(function ($artisan) {
            $artisan->add(new ThrowExceptionCommand);
        });
    }
}

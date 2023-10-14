<?php

namespace QuantaForge\Tests\Integration\Foundation\Fixtures\Providers;

use QuantaForge\Console\Application;
use QuantaForge\Support\ServiceProvider;
use QuantaForge\Tests\Integration\Foundation\Fixtures\Console\ThrowExceptionCommand;

class ThrowExceptionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Application::starting(function ($artisan) {
            $artisan->add(new ThrowExceptionCommand);
        });
    }
}

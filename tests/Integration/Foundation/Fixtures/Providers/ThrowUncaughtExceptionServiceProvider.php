<?php

namespace QuantaQuirk\Tests\Integration\Foundation\Fixtures\Providers;

use QuantaQuirk\Console\Application;
use QuantaQuirk\Support\ServiceProvider;
use QuantaQuirk\Tests\Integration\Foundation\Fixtures\Console\ThrowExceptionCommand;
use QuantaQuirk\Tests\Integration\Foundation\Fixtures\Logs\ThrowExceptionLogHandler;

class ThrowUncaughtExceptionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $config = $this->app['config'];

        $config->set('logging.default', 'throw_exception');

        $config->set('logging.channels.throw_exception', [
            'driver' => 'monolog',
            'handler' => ThrowExceptionLogHandler::class,
        ]);
    }

    public function boot()
    {
        Application::starting(function ($artisan) {
            $artisan->add(new ThrowExceptionCommand);
        });
    }
}

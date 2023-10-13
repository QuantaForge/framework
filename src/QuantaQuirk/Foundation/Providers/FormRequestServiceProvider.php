<?php

namespace QuantaQuirk\Foundation\Providers;

use QuantaQuirk\Contracts\Validation\ValidatesWhenResolved;
use QuantaQuirk\Foundation\Http\FormRequest;
use QuantaQuirk\Routing\Redirector;
use QuantaQuirk\Support\ServiceProvider;

class FormRequestServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->afterResolving(ValidatesWhenResolved::class, function ($resolved) {
            $resolved->validateResolved();
        });

        $this->app->resolving(FormRequest::class, function ($request, $app) {
            $request = FormRequest::createFrom($app['request'], $request);

            $request->setContainer($app)->setRedirector($app->make(Redirector::class));
        });
    }
}

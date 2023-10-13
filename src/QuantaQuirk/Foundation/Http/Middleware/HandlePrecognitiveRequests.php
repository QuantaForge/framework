<?php

namespace QuantaQuirk\Foundation\Http\Middleware;

use QuantaQuirk\Container\Container;
use QuantaQuirk\Foundation\Routing\PrecognitionCallableDispatcher;
use QuantaQuirk\Foundation\Routing\PrecognitionControllerDispatcher;
use QuantaQuirk\Routing\Contracts\CallableDispatcher as CallableDispatcherContract;
use QuantaQuirk\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;

class HandlePrecognitiveRequests
{
    /**
     * The container instance.
     *
     * @var \QuantaQuirk\Container\Container
     */
    protected $container;

    /**
     * Create a new middleware instance.
     *
     * @param  \QuantaQuirk\Container\Container  $container
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \QuantaQuirk\Http\Request  $request
     * @param  \Closure  $next
     * @return \QuantaQuirk\Http\Response
     */
    public function handle($request, $next)
    {
        if (! $request->isAttemptingPrecognition()) {
            return $this->appendVaryHeader($request, $next($request));
        }

        $this->prepareForPrecognition($request);

        return tap($next($request), function ($response) use ($request) {
            $response->headers->set('Precognition', 'true');

            $this->appendVaryHeader($request, $response);
        });
    }

    /**
     * Prepare to handle a precognitive request.
     *
     * @param  \QuantaQuirk\Http\Request  $request
     * @return void
     */
    protected function prepareForPrecognition($request)
    {
        $request->attributes->set('precognitive', true);

        $this->container->bind(CallableDispatcherContract::class, fn ($app) => new PrecognitionCallableDispatcher($app));
        $this->container->bind(ControllerDispatcherContract::class, fn ($app) => new PrecognitionControllerDispatcher($app));
    }

    /**
     * Append the appropriate "Vary" header to the given response.
     *
     * @param  \QuantaQuirk\Http\Request  $request
     * @param  \QuantaQuirk\Http\Response  $response
     * @return \QuantaQuirk\Http\Response
     */
    protected function appendVaryHeader($request, $response)
    {
        return tap($response, fn () => $response->headers->set('Vary', implode(', ', array_filter([
            $response->headers->get('Vary'),
            'Precognition',
        ]))));
    }
}

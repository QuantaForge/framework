<?php

namespace QuantaForge\Foundation\Http;

use Carbon\CarbonInterval;
use DateTimeInterface;
use QuantaForge\Contracts\Debug\ExceptionHandler;
use QuantaForge\Contracts\Foundation\Application;
use QuantaForge\Contracts\Http\Kernel as KernelContract;
use QuantaForge\Foundation\Http\Events\RequestHandled;
use QuantaForge\Routing\Pipeline;
use QuantaForge\Routing\Router;
use QuantaForge\Support\Carbon;
use QuantaForge\Support\Facades\Facade;
use QuantaForge\Support\InteractsWithTime;
use InvalidArgumentException;
use Throwable;

class Kernel implements KernelContract
{
    use InteractsWithTime;

    /**
     * The application implementation.
     *
     * @var \QuantaForge\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The router instance.
     *
     * @var \QuantaForge\Routing\Router
     */
    protected $router;

    /**
     * The bootstrap classes for the application.
     *
     * @var string[]
     */
    protected $bootstrappers = [
        \QuantaForge\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \QuantaForge\Foundation\Bootstrap\LoadConfiguration::class,
        \QuantaForge\Foundation\Bootstrap\HandleExceptions::class,
        \QuantaForge\Foundation\Bootstrap\RegisterFacades::class,
        \QuantaForge\Foundation\Bootstrap\RegisterProviders::class,
        \QuantaForge\Foundation\Bootstrap\BootProviders::class,
    ];

    /**
     * The application's middleware stack.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [];

    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     *
     * @deprecated
     */
    protected $routeMiddleware = [];

    /**
     * The application's middleware aliases.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [];

    /**
     * All of the registered request duration handlers.
     *
     * @var array
     */
    protected $requestLifecycleDurationHandlers = [];

    /**
     * When the kernel starting handling the current request.
     *
     * @var \QuantaForge\Support\Carbon|null
     */
    protected $requestStartedAt;

    /**
     * The priority-sorted list of middleware.
     *
     * Forces non-global middleware to always be in the given order.
     *
     * @var string[]
     */
    protected $middlewarePriority = [
        \QuantaForge\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        \QuantaForge\Cookie\Middleware\EncryptCookies::class,
        \QuantaForge\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \QuantaForge\Session\Middleware\StartSession::class,
        \QuantaForge\View\Middleware\ShareErrorsFromSession::class,
        \QuantaForge\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        \QuantaForge\Routing\Middleware\ThrottleRequests::class,
        \QuantaForge\Routing\Middleware\ThrottleRequestsWithRedis::class,
        \QuantaForge\Contracts\Session\Middleware\AuthenticatesSessions::class,
        \QuantaForge\Routing\Middleware\SubstituteBindings::class,
        \QuantaForge\Auth\Middleware\Authorize::class,
    ];

    /**
     * Create a new HTTP kernel instance.
     *
     * @param  \QuantaForge\Contracts\Foundation\Application  $app
     * @param  \QuantaForge\Routing\Router  $router
     * @return void
     */
    public function __construct(Application $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;

        $this->syncMiddlewareToRouter();
    }

    /**
     * Handle an incoming HTTP request.
     *
     * @param  \QuantaForge\Http\Request  $request
     * @return \QuantaForge\Http\Response
     */
    public function handle($request)
    {
        $this->requestStartedAt = Carbon::now();

        try {
            $request->enableHttpMethodParameterOverride();

            $response = $this->sendRequestThroughRouter($request);
        } catch (Throwable $e) {
            $this->reportException($e);

            $response = $this->renderException($request, $e);
        }

        $this->app['events']->dispatch(
            new RequestHandled($request, $response)
        );

        return $response;
    }

    /**
     * Send the given request through the middleware / router.
     *
     * @param  \QuantaForge\Http\Request  $request
     * @return \QuantaForge\Http\Response
     */
    protected function sendRequestThroughRouter($request)
    {
        $this->app->instance('request', $request);

        Facade::clearResolvedInstance('request');

        $this->bootstrap();

        return (new Pipeline($this->app))
                    ->send($request)
                    ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
                    ->then($this->dispatchToRouter());
    }

    /**
     * Bootstrap the application for HTTP requests.
     *
     * @return void
     */
    public function bootstrap()
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

    /**
     * Get the route dispatcher callback.
     *
     * @return \Closure
     */
    protected function dispatchToRouter()
    {
        return function ($request) {
            $this->app->instance('request', $request);

            return $this->router->dispatch($request);
        };
    }

    /**
     * Call the terminate method on any terminable middleware.
     *
     * @param  \QuantaForge\Http\Request  $request
     * @param  \QuantaForge\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        $this->terminateMiddleware($request, $response);

        $this->app->terminate();

        if ($this->requestStartedAt === null) {
            return;
        }

        $this->requestStartedAt->setTimezone($this->app['config']->get('app.timezone') ?? 'UTC');

        foreach ($this->requestLifecycleDurationHandlers as ['threshold' => $threshold, 'handler' => $handler]) {
            $end ??= Carbon::now();

            if ($this->requestStartedAt->diffInMilliseconds($end) > $threshold) {
                $handler($this->requestStartedAt, $request, $response);
            }
        }

        $this->requestStartedAt = null;
    }

    /**
     * Call the terminate method on any terminable middleware.
     *
     * @param  \QuantaForge\Http\Request  $request
     * @param  \QuantaForge\Http\Response  $response
     * @return void
     */
    protected function terminateMiddleware($request, $response)
    {
        $middlewares = $this->app->shouldSkipMiddleware() ? [] : array_merge(
            $this->gatherRouteMiddleware($request),
            $this->middleware
        );

        foreach ($middlewares as $middleware) {
            if (! is_string($middleware)) {
                continue;
            }

            [$name] = $this->parseMiddleware($middleware);

            $instance = $this->app->make($name);

            if (method_exists($instance, 'terminate')) {
                $instance->terminate($request, $response);
            }
        }
    }

    /**
     * Register a callback to be invoked when the requests lifecycle duration exceeds a given amount of time.
     *
     * @param  \DateTimeInterface|\Carbon\CarbonInterval|float|int  $threshold
     * @param  callable  $handler
     * @return void
     */
    public function whenRequestLifecycleIsLongerThan($threshold, $handler)
    {
        $threshold = $threshold instanceof DateTimeInterface
            ? $this->secondsUntil($threshold) * 1000
            : $threshold;

        $threshold = $threshold instanceof CarbonInterval
            ? $threshold->totalMilliseconds
            : $threshold;

        $this->requestLifecycleDurationHandlers[] = [
            'threshold' => $threshold,
            'handler' => $handler,
        ];
    }

    /**
     * When the request being handled started.
     *
     * @return \QuantaForge\Support\Carbon|null
     */
    public function requestStartedAt()
    {
        return $this->requestStartedAt;
    }

    /**
     * Gather the route middleware for the given request.
     *
     * @param  \QuantaForge\Http\Request  $request
     * @return array
     */
    protected function gatherRouteMiddleware($request)
    {
        if ($route = $request->route()) {
            return $this->router->gatherRouteMiddleware($route);
        }

        return [];
    }

    /**
     * Parse a middleware string to get the name and parameters.
     *
     * @param  string  $middleware
     * @return array
     */
    protected function parseMiddleware($middleware)
    {
        [$name, $parameters] = array_pad(explode(':', $middleware, 2), 2, []);

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
    }

    /**
     * Determine if the kernel has a given middleware.
     *
     * @param  string  $middleware
     * @return bool
     */
    public function hasMiddleware($middleware)
    {
        return in_array($middleware, $this->middleware);
    }

    /**
     * Add a new middleware to the beginning of the stack if it does not already exist.
     *
     * @param  string  $middleware
     * @return $this
     */
    public function prependMiddleware($middleware)
    {
        if (array_search($middleware, $this->middleware) === false) {
            array_unshift($this->middleware, $middleware);
        }

        return $this;
    }

    /**
     * Add a new middleware to end of the stack if it does not already exist.
     *
     * @param  string  $middleware
     * @return $this
     */
    public function pushMiddleware($middleware)
    {
        if (array_search($middleware, $this->middleware) === false) {
            $this->middleware[] = $middleware;
        }

        return $this;
    }

    /**
     * Prepend the given middleware to the given middleware group.
     *
     * @param  string  $group
     * @param  string  $middleware
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function prependMiddlewareToGroup($group, $middleware)
    {
        if (! isset($this->middlewareGroups[$group])) {
            throw new InvalidArgumentException("The [{$group}] middleware group has not been defined.");
        }

        if (array_search($middleware, $this->middlewareGroups[$group]) === false) {
            array_unshift($this->middlewareGroups[$group], $middleware);
        }

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * Append the given middleware to the given middleware group.
     *
     * @param  string  $group
     * @param  string  $middleware
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function appendMiddlewareToGroup($group, $middleware)
    {
        if (! isset($this->middlewareGroups[$group])) {
            throw new InvalidArgumentException("The [{$group}] middleware group has not been defined.");
        }

        if (array_search($middleware, $this->middlewareGroups[$group]) === false) {
            $this->middlewareGroups[$group][] = $middleware;
        }

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * Prepend the given middleware to the middleware priority list.
     *
     * @param  string  $middleware
     * @return $this
     */
    public function prependToMiddlewarePriority($middleware)
    {
        if (! in_array($middleware, $this->middlewarePriority)) {
            array_unshift($this->middlewarePriority, $middleware);
        }

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * Append the given middleware to the middleware priority list.
     *
     * @param  string  $middleware
     * @return $this
     */
    public function appendToMiddlewarePriority($middleware)
    {
        if (! in_array($middleware, $this->middlewarePriority)) {
            $this->middlewarePriority[] = $middleware;
        }

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * Sync the current state of the middleware to the router.
     *
     * @return void
     */
    protected function syncMiddlewareToRouter()
    {
        $this->router->middlewarePriority = $this->middlewarePriority;

        foreach ($this->middlewareGroups as $key => $middleware) {
            $this->router->middlewareGroup($key, $middleware);
        }

        foreach (array_merge($this->routeMiddleware, $this->middlewareAliases) as $key => $middleware) {
            $this->router->aliasMiddleware($key, $middleware);
        }
    }

    /**
     * Get the priority-sorted list of middleware.
     *
     * @return array
     */
    public function getMiddlewarePriority()
    {
        return $this->middlewarePriority;
    }

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Throwable  $e
     * @return void
     */
    protected function reportException(Throwable $e)
    {
        $this->app[ExceptionHandler::class]->report($e);
    }

    /**
     * Render the exception to a response.
     *
     * @param  \QuantaForge\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderException($request, Throwable $e)
    {
        return $this->app[ExceptionHandler::class]->render($request, $e);
    }

    /**
     * Get the application's route middleware groups.
     *
     * @return array
     */
    public function getMiddlewareGroups()
    {
        return $this->middlewareGroups;
    }

    /**
     * Get the application's route middleware aliases.
     *
     * @return array
     *
     * @deprecated
     */
    public function getRouteMiddleware()
    {
        return $this->getMiddlewareAliases();
    }

    /**
     * Get the application's route middleware aliases.
     *
     * @return array
     */
    public function getMiddlewareAliases()
    {
        return array_merge($this->routeMiddleware, $this->middlewareAliases);
    }

    /**
     * Get the QuantaForge application instance.
     *
     * @return \QuantaForge\Contracts\Foundation\Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Set the QuantaForge application instance.
     *
     * @param  \QuantaForge\Contracts\Foundation\Application  $app
     * @return $this
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;

        return $this;
    }
}

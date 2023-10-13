<?php

namespace QuantaQuirk\Tests\Foundation\Http;

use QuantaQuirk\Events\Dispatcher;
use QuantaQuirk\Foundation\Application;
use QuantaQuirk\Foundation\Http\Kernel;
use QuantaQuirk\Routing\Router;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    public function testGetMiddlewareGroups()
    {
        $kernel = new Kernel($this->getApplication(), $this->getRouter());

        $this->assertEquals([], $kernel->getMiddlewareGroups());
    }

    public function testGetRouteMiddleware()
    {
        $kernel = new Kernel($this->getApplication(), $this->getRouter());

        $this->assertEquals([], $kernel->getRouteMiddleware());
    }

    public function testGetMiddlewarePriority()
    {
        $kernel = new Kernel($this->getApplication(), $this->getRouter());

        $this->assertEquals([
            \QuantaQuirk\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \QuantaQuirk\Cookie\Middleware\EncryptCookies::class,
            \QuantaQuirk\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \QuantaQuirk\Session\Middleware\StartSession::class,
            \QuantaQuirk\View\Middleware\ShareErrorsFromSession::class,
            \QuantaQuirk\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \QuantaQuirk\Routing\Middleware\ThrottleRequests::class,
            \QuantaQuirk\Routing\Middleware\ThrottleRequestsWithRedis::class,
            \QuantaQuirk\Contracts\Session\Middleware\AuthenticatesSessions::class,
            \QuantaQuirk\Routing\Middleware\SubstituteBindings::class,
            \QuantaQuirk\Auth\Middleware\Authorize::class,
        ], $kernel->getMiddlewarePriority());
    }

    /**
     * @return \QuantaQuirk\Contracts\Foundation\Application
     */
    protected function getApplication()
    {
        return new Application;
    }

    /**
     * @return \QuantaQuirk\Routing\Router
     */
    protected function getRouter()
    {
        return new Router(new Dispatcher);
    }
}

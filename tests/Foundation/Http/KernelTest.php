<?php

namespace QuantaForge\Tests\Foundation\Http;

use QuantaForge\Events\Dispatcher;
use QuantaForge\Foundation\Application;
use QuantaForge\Foundation\Http\Kernel;
use QuantaForge\Routing\Router;
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
        ], $kernel->getMiddlewarePriority());
    }

    /**
     * @return \QuantaForge\Contracts\Foundation\Application
     */
    protected function getApplication()
    {
        return new Application;
    }

    /**
     * @return \QuantaForge\Routing\Router
     */
    protected function getRouter()
    {
        return new Router(new Dispatcher);
    }
}

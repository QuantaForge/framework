<?php

namespace QuantaForge\Tests\Integration\Session;

use QuantaForge\Contracts\Debug\ExceptionHandler;
use QuantaForge\Http\Response;
use QuantaForge\Session\NullSessionHandler;
use QuantaForge\Session\TokenMismatchException;
use QuantaForge\Support\Facades\Route;
use QuantaForge\Support\Facades\Session;
use QuantaForge\Support\Str;
use Mockery as m;
use Orchestra\Testbench\TestCase;

class SessionPersistenceTest extends TestCase
{
    public function testSessionIsPersistedEvenIfExceptionIsThrownFromRoute()
    {
        $handler = new FakeNullSessionHandler;
        $this->assertFalse($handler->written);

        Session::extend('fake-null', function () use ($handler) {
            return $handler;
        });

        Route::get('/', function () {
            throw new TokenMismatchException;
        })->middleware('web');

        $this->get('/');
        $this->assertTrue($handler->written);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->instance(
            ExceptionHandler::class,
            $handler = m::mock(ExceptionHandler::class)->shouldIgnoreMissing()
        );

        $handler->shouldReceive('render')->andReturn(new Response);

        $app['config']->set('app.key', Str::random(32));
        $app['config']->set('session.driver', 'fake-null');
        $app['config']->set('session.expire_on_close', true);
    }
}

class FakeNullSessionHandler extends NullSessionHandler
{
    public $written = false;

    public function write($sessionId, $data): bool
    {
        $this->written = true;

        return true;
    }
}

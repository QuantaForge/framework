<?php

namespace QuantaForge\Tests\Integration\Session;

use QuantaForge\Support\Facades\Route;
use QuantaForge\Support\Str;
use Orchestra\Testbench\TestCase;

class CookieSessionHandlerTest extends TestCase
{
    public function testCookieSessionDriverCookiesCanExpireOnClose()
    {
        Route::get('/', fn () => '')->middleware('web');

        $response = $this->get('/');
        $sessionIdCookie = $response->getCookie('quantaforge_session');
        $sessionValueCookie = $response->getCookie($sessionIdCookie->getValue());

        $this->assertEquals(0, $sessionIdCookie->getExpiresTime());
        $this->assertEquals(0, $sessionValueCookie->getExpiresTime());
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', Str::random(32));
        $app['config']->set('session.driver', 'cookie');
        $app['config']->set('session.expire_on_close', true);
    }
}

<?php

namespace QuantaQuirk\Tests\Integration\Session;

use QuantaQuirk\Support\Facades\Route;
use QuantaQuirk\Support\Str;
use Orchestra\Testbench\TestCase;

class CookieSessionHandlerTest extends TestCase
{
    public function testCookieSessionDriverCookiesCanExpireOnClose()
    {
        Route::get('/', fn () => '')->middleware('web');

        $response = $this->get('/');
        $sessionIdCookie = $response->getCookie('quantaquirk_session');
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

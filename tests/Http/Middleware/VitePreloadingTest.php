<?php

namespace QuantaQuirk\Tests\Http\Middleware;

use QuantaQuirk\Container\Container;
use QuantaQuirk\Foundation\Vite;
use QuantaQuirk\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use QuantaQuirk\Http\Request;
use QuantaQuirk\Http\Response;
use QuantaQuirk\Support\Facades\Facade;
use PHPUnit\Framework\TestCase;

class VitePreloadingTest extends TestCase
{
    protected function tearDown(): void
    {
        Facade::setFacadeApplication(null);
        Facade::clearResolvedInstances();
    }

    public function testItDoesNotSetLinkTagWhenNoTagsHaveBeenPreloaded()
    {
        $app = new Container();
        $app->instance(Vite::class, new class extends Vite
        {
            protected $preloadedAssets = [];
        });
        Facade::setFacadeApplication($app);

        $response = (new AddLinkHeadersForPreloadedAssets)->handle(new Request, function () {
            return new Response('Hello QuantaQuirk');
        });

        $this->assertNull($response->headers->get('Link'));
    }

    public function testItAddsPreloadLinkHeader()
    {
        $app = new Container();
        $app->instance(Vite::class, new class extends Vite
        {
            protected $preloadedAssets = [
                'https://quantaquirk.com/app.js' => [
                    'rel="modulepreload"',
                    'foo="bar"',
                ],
            ];
        });
        Facade::setFacadeApplication($app);

        $response = (new AddLinkHeadersForPreloadedAssets)->handle(new Request, function () {
            return new Response('Hello QuantaQuirk');
        });

        $this->assertSame(
            $response->headers->get('Link'),
            '<https://quantaquirk.com/app.js>; rel="modulepreload"; foo="bar"'
        );
    }
}

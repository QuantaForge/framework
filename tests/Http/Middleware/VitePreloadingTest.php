<?php

namespace QuantaForge\Tests\Http\Middleware;

use QuantaForge\Container\Container;
use QuantaForge\Foundation\Vite;
use QuantaForge\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use QuantaForge\Http\Request;
use QuantaForge\Http\Response;
use QuantaForge\Support\Facades\Facade;
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
            return new Response('Hello QuantaForge');
        });

        $this->assertNull($response->headers->get('Link'));
    }

    public function testItAddsPreloadLinkHeader()
    {
        $app = new Container();
        $app->instance(Vite::class, new class extends Vite
        {
            protected $preloadedAssets = [
                'https://quantaforge.com/app.js' => [
                    'rel="modulepreload"',
                    'foo="bar"',
                ],
            ];
        });
        Facade::setFacadeApplication($app);

        $response = (new AddLinkHeadersForPreloadedAssets)->handle(new Request, function () {
            return new Response('Hello QuantaForge');
        });

        $this->assertSame(
            $response->headers->get('Link'),
            '<https://quantaforge.com/app.js>; rel="modulepreload"; foo="bar"'
        );
    }
}

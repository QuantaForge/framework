<?php

namespace QuantaQuirk\Tests\Integration\Http;

use QuantaQuirk\Support\Facades\Facade;
use QuantaQuirk\Support\Facades\Http;
use Orchestra\Testbench\TestCase;

class HttpClientTest extends TestCase
{
    public function testGlobalMiddlewarePersistsAfterFacadeFlush(): void
    {
        Http::macro('getGlobalMiddleware', fn () => $this->globalMiddleware);
        Http::globalRequestMiddleware(fn ($request) => $request->withHeader('User-Agent', 'Example Application/1.0'));
        Http::globalRequestMiddleware(fn ($request) => $request->withHeader('User-Agent', 'Example Application/1.0'));

        $this->assertCount(2, Http::getGlobalMiddleware());

        Facade::clearResolvedInstances();

        $this->assertCount(2, Http::getGlobalMiddleware());
    }
}

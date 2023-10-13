<?php

namespace QuantaQuirk\Tests\Integration\Routing;

use QuantaQuirk\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class SimpleRouteTest extends TestCase
{
    public function testSimpleRouteThroughTheFramework()
    {
        Route::get('/', function () {
            return 'Hello World';
        });

        $response = $this->get('/');

        $this->assertSame('Hello World', $response->content());
    }
}

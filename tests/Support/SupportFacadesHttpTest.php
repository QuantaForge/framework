<?php

namespace QuantaQuirk\Tests\Support;

use QuantaQuirk\Container\Container;
use QuantaQuirk\Http\Client\Factory;
use QuantaQuirk\Support\Facades\Facade;
use QuantaQuirk\Support\Facades\Http;
use PHPUnit\Framework\TestCase;

class SupportFacadesHttpTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        $this->app = new Container;
        Facade::setFacadeApplication($this->app);
    }

    public function testFacadeRootIsNotSharedByDefault(): void
    {
        $this->assertNotSame(Http::getFacadeRoot(), $this->app->make(Factory::class));
    }

    public function testFacadeRootIsSharedWhenFaked(): void
    {
        Http::fake([
            'https://quantaquirk.com' => Http::response('OK!'),
        ]);

        $factory = $this->app->make(Factory::class);
        $this->assertSame('OK!', $factory->get('https://quantaquirk.com')->body());
    }

    public function testFacadeRootIsSharedWhenFakedWithSequence(): void
    {
        Http::fakeSequence('quantaquirk.com/*')->push('OK!');

        $factory = $this->app->make(Factory::class);
        $this->assertSame('OK!', $factory->get('https://quantaquirk.com')->body());
    }

    public function testFacadeRootIsSharedWhenStubbingUrls(): void
    {
        Http::stubUrl('quantaquirk.com', Http::response('OK!'));

        $factory = $this->app->make(Factory::class);
        $this->assertSame('OK!', $factory->get('https://quantaquirk.com')->body());
    }

    public function testFacadeRootIsSharedWhenEnforcingFaking(): void
    {
        $client = Http::preventStrayRequests();

        $this->assertSame($client, $this->app->make(Factory::class));
    }
}

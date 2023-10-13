<?php

namespace QuantaQuirk\Tests\Routing;

use QuantaQuirk\Routing\RouteUri;
use PHPUnit\Framework\TestCase;

class RouteUriTest extends TestCase
{
    /**
     * @dataProvider uriProvider
     */
    public function testRouteUrisAreProperlyParsed($uri, $expectedParsedUri, $expectedBindingFields)
    {
        $parsed = RouteUri::parse($uri);
        $this->assertSame($expectedParsedUri, $parsed->uri);
        $this->assertEquals($expectedBindingFields, $parsed->bindingFields);
    }

    /**
     * @return array
     */
    public static function uriProvider()
    {
        return [
            [
                '/foo',
                '/foo',
                [],
            ],
            [
                '/foo/{bar}',
                '/foo/{bar}',
                [],
            ],
            [
                '/foo/{bar}/baz/{qux}',
                '/foo/{bar}/baz/{qux}',
                [],
            ],
            [
                '/foo/{bar}/baz/{qux?}',
                '/foo/{bar}/baz/{qux?}',
                [],
            ],
            [
                '/foo/{bar:slug}',
                '/foo/{bar}',
                ['bar' => 'slug'],
            ],
            [
                '/foo/{bar}/baz/{qux:slug}',
                '/foo/{bar}/baz/{qux}',
                ['qux' => 'slug'],
            ],
            [
                '/foo/{bar}/baz/{qux:slug}',
                '/foo/{bar}/baz/{qux}',
                ['qux' => 'slug'],
            ],
            [
                '/foo/{bar}/baz/{qux:slug?}',
                '/foo/{bar}/baz/{qux?}',
                ['qux' => 'slug'],
            ],
            [
                '/foo/{bar}/baz/{qux:slug?}/{test:id?}',
                '/foo/{bar}/baz/{qux?}/{test?}',
                ['qux' => 'slug', 'test' => 'id'],
            ],
        ];
    }
}

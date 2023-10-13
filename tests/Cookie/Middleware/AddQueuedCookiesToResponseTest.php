<?php

namespace QuantaQuirk\Tests\Cookie\Middleware;

use QuantaQuirk\Cookie\CookieJar;
use QuantaQuirk\Cookie\Middleware\AddQueuedCookiesToResponse;
use QuantaQuirk\Http\Request;
use QuantaQuirk\Http\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AddQueuedCookiesToResponseTest extends TestCase
{
    public function testHandle(): void
    {
        $cookieJar = new CookieJar;
        $cookieOne = $cookieJar->make('foo', 'bar', 0, '/path');
        $cookieTwo = $cookieJar->make('foo', 'rab', 0, '/');
        $cookieJar->queue($cookieOne);
        $cookieJar->queue($cookieTwo);
        $addQueueCookiesToResponseMiddleware = new AddQueuedCookiesToResponse($cookieJar);
        $next = function (Request $request) {
            return new Response;
        };
        $this->assertEquals(
            [
                '' => [
                    '/path' => [
                        'foo' => $cookieOne,
                    ],
                    '/' => [
                        'foo' => $cookieTwo,
                    ],
                ],
            ],
            $addQueueCookiesToResponseMiddleware->handle(new Request, $next)->headers->getCookies(ResponseHeaderBag::COOKIES_ARRAY)
        );
    }
}

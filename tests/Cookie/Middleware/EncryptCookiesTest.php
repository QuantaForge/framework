<?php

namespace QuantaForge\Tests\Cookie\Middleware;

use QuantaForge\Container\Container;
use QuantaForge\Contracts\Encryption\Encrypter as EncrypterContract;
use QuantaForge\Cookie\CookieJar;
use QuantaForge\Cookie\CookieValuePrefix;
use QuantaForge\Cookie\Middleware\AddQueuedCookiesToResponse;
use QuantaForge\Cookie\Middleware\EncryptCookies;
use QuantaForge\Encryption\Encrypter;
use QuantaForge\Events\Dispatcher;
use QuantaForge\Http\Request;
use QuantaForge\Http\Response;
use QuantaForge\Routing\Controller;
use QuantaForge\Routing\Router;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Cookie;

class EncryptCookiesTest extends TestCase
{
    /**
     * @var \QuantaForge\Container\Container
     */
    protected $container;

    /**
     * @var \QuantaForge\Routing\Router
     */
    protected $router;

    protected $setCookiePath = 'cookie/set';
    protected $queueCookiePath = 'cookie/queue';

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new Container;
        $this->container->singleton(EncrypterContract::class, function () {
            return new Encrypter(str_repeat('a', 16));
        });

        $this->router = new Router(new Dispatcher, $this->container);
    }

    public function testSetCookieEncryption()
    {
        $this->router->get($this->setCookiePath, [
            'middleware' => EncryptCookiesTestMiddleware::class,
            'uses' => EncryptCookiesTestController::class.'@setCookies',
        ]);

        $response = $this->router->dispatch(Request::create($this->setCookiePath, 'GET'));

        $cookies = $response->headers->getCookies();
        $this->assertCount(4, $cookies);
        $this->assertSame('encrypted_cookie', $cookies[0]->getName());
        $this->assertNotSame('value', $cookies[0]->getValue());
        $this->assertSame('encrypted[array_cookie]', $cookies[1]->getName());
        $this->assertNotSame('value', $cookies[1]->getValue());
        $this->assertSame('encrypted[nested][array_cookie]', $cookies[2]->getName());
        $this->assertSame('unencrypted_cookie', $cookies[3]->getName());
        $this->assertSame('value', $cookies[3]->getValue());
    }

    public function testQueuedCookieEncryption()
    {
        $this->router->get($this->queueCookiePath, [
            'middleware' => [EncryptCookiesTestMiddleware::class, AddQueuedCookiesToResponseTestMiddleware::class],
            'uses' => EncryptCookiesTestController::class.'@queueCookies',
        ]);

        $response = $this->router->dispatch(Request::create($this->queueCookiePath, 'GET'));

        $cookies = $response->headers->getCookies();
        $this->assertCount(4, $cookies);
        $this->assertSame('encrypted_cookie', $cookies[0]->getName());
        $this->assertNotSame('value', $cookies[0]->getValue());
        $this->assertSame('encrypted[array_cookie]', $cookies[1]->getName());
        $this->assertNotSame('value', $cookies[1]->getValue());
        $this->assertSame('encrypted[nested][array_cookie]', $cookies[2]->getName());
        $this->assertNotSame('value', $cookies[2]->getValue());
        $this->assertSame('unencrypted_cookie', $cookies[3]->getName());
        $this->assertSame('value', $cookies[3]->getValue());
    }

    protected function getEncryptedCookieValue($key, $value)
    {
        $encrypter = $this->container->make(EncrypterContract::class);

        return $encrypter->encrypt(
            CookieValuePrefix::create($key, $encrypter->getKey()).$value,
            false
        );
    }

    public function testCookieDecryption()
    {
        $cookies = [
            'encrypted_cookie' => $this->getEncryptedCookieValue('encrypted_cookie', 'value'),
            'encrypted' => [
                'array_cookie' => $this->getEncryptedCookieValue('encrypted[array_cookie]', 'value'),
                'nested' => [
                    'array_cookie' => $this->getEncryptedCookieValue('encrypted[nested][array_cookie]', 'value'),
                ],
            ],
            'unencrypted_cookie' => 'value',
        ];

        $this->container->make(EncryptCookiesTestMiddleware::class)->handle(
            Request::create('/cookie/read', 'GET', [], $cookies),
            function ($request) {
                $cookies = $request->cookies->all();
                $this->assertCount(3, $cookies);
                $this->assertArrayHasKey('encrypted_cookie', $cookies);
                $this->assertSame('value', $cookies['encrypted_cookie']);
                $this->assertArrayHasKey('encrypted', $cookies);
                $this->assertArrayHasKey('array_cookie', $cookies['encrypted']);
                $this->assertSame('value', $cookies['encrypted']['array_cookie']);
                $this->assertArrayHasKey('nested', $cookies['encrypted']);
                $this->assertArrayHasKey('array_cookie', $cookies['encrypted']['nested']);
                $this->assertSame('value', $cookies['encrypted']['nested']['array_cookie']);
                $this->assertArrayHasKey('unencrypted_cookie', $cookies);
                $this->assertSame('value', $cookies['unencrypted_cookie']);

                return new Response;
            }
        );
    }
}

class EncryptCookiesTestController extends Controller
{
    public function setCookies()
    {
        $response = new Response;
        $response->headers->setCookie(new Cookie('encrypted_cookie', 'value'));
        $response->headers->setCookie(new Cookie('encrypted[array_cookie]', 'value'));
        $response->headers->setCookie(new Cookie('encrypted[nested][array_cookie]', 'value'));
        $response->headers->setCookie(new Cookie('unencrypted_cookie', 'value'));

        return $response;
    }

    public function queueCookies()
    {
        return new Response;
    }
}

class EncryptCookiesTestMiddleware extends EncryptCookies
{
    protected $except = [
        'unencrypted_cookie',
    ];
}

class AddQueuedCookiesToResponseTestMiddleware extends AddQueuedCookiesToResponse
{
    public function __construct()
    {
        $cookie = new CookieJar;
        $cookie->queue(new Cookie('encrypted_cookie', 'value'));
        $cookie->queue(new Cookie('encrypted[array_cookie]', 'value'));
        $cookie->queue(new Cookie('encrypted[nested][array_cookie]', 'value'));
        $cookie->queue(new Cookie('unencrypted_cookie', 'value'));

        $this->cookies = $cookie;
    }
}

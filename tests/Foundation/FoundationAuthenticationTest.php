<?php

namespace QuantaForge\Tests\Foundation;

use QuantaForge\Auth\AuthManager;
use QuantaForge\Contracts\Auth\Authenticatable;
use QuantaForge\Contracts\Auth\Guard;
use QuantaForge\Contracts\Auth\UserProvider;
use QuantaForge\Foundation\Application;
use QuantaForge\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class FoundationAuthenticationTest extends TestCase
{
    use InteractsWithAuthentication;

    /**
     * @var \Mockery
     */
    protected $app;

    /**
     * @return array
     */
    protected $credentials = [
        'email' => 'someone@quantaforge.com',
        'password' => 'secret_password',
    ];

    /**
     * @return \QuantaForge\Contracts\Auth\Guard|\Mockery\LegacyMockInterface|\Mockery\MockInterface
     */
    protected function mockGuard()
    {
        $guard = m::mock(Guard::class);

        $auth = m::mock(AuthManager::class);
        $auth->shouldReceive('guard')
            ->once()
            ->andReturn($guard);

        $this->app = m::mock(Application::class);
        $this->app->shouldReceive('make')
            ->once()
            ->withArgs(['auth'])
            ->andReturn($auth);

        return $guard;
    }

    protected function tearDown(): void
    {
        m::close();
    }

    public function testAssertAuthenticated()
    {
        $this->mockGuard()
            ->shouldReceive('check')
            ->once()
            ->andReturn(true);

        $this->assertAuthenticated();
    }

    public function testAssertGuest()
    {
        $this->mockGuard()
            ->shouldReceive('check')
            ->once()
            ->andReturn(false);

        $this->assertGuest();
    }

    public function testAssertAuthenticatedAs()
    {
        $expected = m::mock(Authenticatable::class);
        $expected->shouldReceive('getAuthIdentifier')
            ->andReturn('1');

        $this->mockGuard()
            ->shouldReceive('user')
            ->once()
            ->andReturn($expected);

        $user = m::mock(Authenticatable::class);
        $user->shouldReceive('getAuthIdentifier')
            ->andReturn('1');

        $this->assertAuthenticatedAs($user);
    }

    protected function setupProvider(array $credentials)
    {
        $user = m::mock(Authenticatable::class);

        $provider = m::mock(UserProvider::class);

        $provider->shouldReceive('retrieveByCredentials')
            ->with($credentials)
            ->andReturn($user);

        $provider->shouldReceive('validateCredentials')
            ->with($user, $credentials)
            ->andReturn($this->credentials === $credentials);

        $this->mockGuard()
            ->shouldReceive('getProvider')
            ->once()
            ->andReturn($provider);
    }

    public function testAssertCredentials()
    {
        $this->setupProvider($this->credentials);

        $this->assertCredentials($this->credentials);
    }

    public function testAssertCredentialsMissing()
    {
        $credentials = [
            'email' => 'invalid',
            'password' => 'credentials',
        ];

        $this->setupProvider($credentials);

        $this->assertInvalidCredentials($credentials);
    }
}

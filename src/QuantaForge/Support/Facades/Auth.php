<?php

namespace QuantaForge\Support\Facades;

use QuantaForge\Ui\UiServiceProvider;
use RuntimeException;

/**
 * @method static \QuantaForge\Contracts\Auth\Guard|\QuantaForge\Contracts\Auth\StatefulGuard guard(string|null $name = null)
 * @method static \QuantaForge\Auth\SessionGuard createSessionDriver(string $name, array $config)
 * @method static \QuantaForge\Auth\TokenGuard createTokenDriver(string $name, array $config)
 * @method static string getDefaultDriver()
 * @method static void shouldUse(string $name)
 * @method static void setDefaultDriver(string $name)
 * @method static \QuantaForge\Auth\AuthManager viaRequest(string $driver, callable $callback)
 * @method static \Closure userResolver()
 * @method static \QuantaForge\Auth\AuthManager resolveUsersUsing(\Closure $userResolver)
 * @method static \QuantaForge\Auth\AuthManager extend(string $driver, \Closure $callback)
 * @method static \QuantaForge\Auth\AuthManager provider(string $name, \Closure $callback)
 * @method static bool hasResolvedGuards()
 * @method static \QuantaForge\Auth\AuthManager forgetGuards()
 * @method static \QuantaForge\Auth\AuthManager setApplication(\QuantaForge\Contracts\Foundation\Application $app)
 * @method static \QuantaForge\Contracts\Auth\UserProvider|null createUserProvider(string|null $provider = null)
 * @method static string getDefaultUserProvider()
 * @method static bool check()
 * @method static bool guest()
 * @method static \QuantaForge\Contracts\Auth\Authenticatable|null user()
 * @method static int|string|null id()
 * @method static bool validate(array $credentials = [])
 * @method static bool hasUser()
 * @method static void setUser(\QuantaForge\Contracts\Auth\Authenticatable $user)
 * @method static bool attempt(array $credentials = [], bool $remember = false)
 * @method static bool once(array $credentials = [])
 * @method static void login(\QuantaForge\Contracts\Auth\Authenticatable $user, bool $remember = false)
 * @method static \QuantaForge\Contracts\Auth\Authenticatable|bool loginUsingId(mixed $id, bool $remember = false)
 * @method static \QuantaForge\Contracts\Auth\Authenticatable|bool onceUsingId(mixed $id)
 * @method static bool viaRemember()
 * @method static void logout()
 * @method static \Symfony\Component\HttpFoundation\Response|null basic(string $field = 'email', array $extraConditions = [])
 * @method static \Symfony\Component\HttpFoundation\Response|null onceBasic(string $field = 'email', array $extraConditions = [])
 * @method static bool attemptWhen(array $credentials = [], array|callable|null $callbacks = null, bool $remember = false)
 * @method static void logoutCurrentDevice()
 * @method static \QuantaForge\Contracts\Auth\Authenticatable|null logoutOtherDevices(string $password, string $attribute = 'password')
 * @method static void attempting(mixed $callback)
 * @method static \QuantaForge\Contracts\Auth\Authenticatable getLastAttempted()
 * @method static string getName()
 * @method static string getRecallerName()
 * @method static \QuantaForge\Auth\SessionGuard setRememberDuration(int $minutes)
 * @method static \QuantaForge\Contracts\Cookie\QueueingFactory getCookieJar()
 * @method static void setCookieJar(\QuantaForge\Contracts\Cookie\QueueingFactory $cookie)
 * @method static \QuantaForge\Contracts\Events\Dispatcher getDispatcher()
 * @method static void setDispatcher(\QuantaForge\Contracts\Events\Dispatcher $events)
 * @method static \QuantaForge\Contracts\Session\Session getSession()
 * @method static \QuantaForge\Contracts\Auth\Authenticatable|null getUser()
 * @method static \Symfony\Component\HttpFoundation\Request getRequest()
 * @method static \QuantaForge\Auth\SessionGuard setRequest(\Symfony\Component\HttpFoundation\Request $request)
 * @method static \QuantaForge\Support\Timebox getTimebox()
 * @method static \QuantaForge\Contracts\Auth\Authenticatable authenticate()
 * @method static \QuantaForge\Auth\SessionGuard forgetUser()
 * @method static \QuantaForge\Contracts\Auth\UserProvider getProvider()
 * @method static void setProvider(\QuantaForge\Contracts\Auth\UserProvider $provider)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \QuantaForge\Auth\AuthManager
 * @see \QuantaForge\Auth\SessionGuard
 */
class Auth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth';
    }

    /**
     * Register the typical authentication routes for an application.
     *
     * @param  array  $options
     * @return void
     *
     * @throws \RuntimeException
     */
    public static function routes(array $options = [])
    {
        if (! static::$app->providerIsLoaded(UiServiceProvider::class)) {
            throw new RuntimeException('In order to use the Auth::routes() method, please install the quantaforge/ui package.');
        }

        static::$app->make('router')->auth($options);
    }
}

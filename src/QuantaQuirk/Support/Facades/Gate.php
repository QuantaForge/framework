<?php

namespace QuantaQuirk\Support\Facades;

use QuantaQuirk\Contracts\Auth\Access\Gate as GateContract;

/**
 * @method static bool has(string|array $ability)
 * @method static \QuantaQuirk\Auth\Access\Response allowIf(\QuantaQuirk\Auth\Access\Response|\Closure|bool $condition, string|null $message = null, string|null $code = null)
 * @method static \QuantaQuirk\Auth\Access\Response denyIf(\QuantaQuirk\Auth\Access\Response|\Closure|bool $condition, string|null $message = null, string|null $code = null)
 * @method static \QuantaQuirk\Auth\Access\Gate define(string $ability, callable|array|string $callback)
 * @method static \QuantaQuirk\Auth\Access\Gate resource(string $name, string $class, array|null $abilities = null)
 * @method static \QuantaQuirk\Auth\Access\Gate policy(string $class, string $policy)
 * @method static \QuantaQuirk\Auth\Access\Gate before(callable $callback)
 * @method static \QuantaQuirk\Auth\Access\Gate after(callable $callback)
 * @method static bool allows(string $ability, array|mixed $arguments = [])
 * @method static bool denies(string $ability, array|mixed $arguments = [])
 * @method static bool check(iterable|string $abilities, array|mixed $arguments = [])
 * @method static bool any(iterable|string $abilities, array|mixed $arguments = [])
 * @method static bool none(iterable|string $abilities, array|mixed $arguments = [])
 * @method static \QuantaQuirk\Auth\Access\Response authorize(string $ability, array|mixed $arguments = [])
 * @method static \QuantaQuirk\Auth\Access\Response inspect(string $ability, array|mixed $arguments = [])
 * @method static mixed raw(string $ability, array|mixed $arguments = [])
 * @method static mixed getPolicyFor(object|string $class)
 * @method static \QuantaQuirk\Auth\Access\Gate guessPolicyNamesUsing(callable $callback)
 * @method static mixed resolvePolicy(object|string $class)
 * @method static \QuantaQuirk\Auth\Access\Gate forUser(\QuantaQuirk\Contracts\Auth\Authenticatable|mixed $user)
 * @method static array abilities()
 * @method static array policies()
 * @method static \QuantaQuirk\Auth\Access\Gate defaultDenialResponse(\QuantaQuirk\Auth\Access\Response $response)
 * @method static \QuantaQuirk\Auth\Access\Gate setContainer(\QuantaQuirk\Contracts\Container\Container $container)
 * @method static \QuantaQuirk\Auth\Access\Response denyWithStatus(int $status, string|null $message = null, int|null $code = null)
 * @method static \QuantaQuirk\Auth\Access\Response denyAsNotFound(string|null $message = null, int|null $code = null)
 *
 * @see \QuantaQuirk\Auth\Access\Gate
 */
class Gate extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return GateContract::class;
    }
}

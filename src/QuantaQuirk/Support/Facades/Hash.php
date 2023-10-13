<?php

namespace QuantaQuirk\Support\Facades;

/**
 * @method static \QuantaQuirk\Hashing\BcryptHasher createBcryptDriver()
 * @method static \QuantaQuirk\Hashing\ArgonHasher createArgonDriver()
 * @method static \QuantaQuirk\Hashing\Argon2IdHasher createArgon2idDriver()
 * @method static array info(string $hashedValue)
 * @method static string make(string $value, array $options = [])
 * @method static bool check(string $value, string $hashedValue, array $options = [])
 * @method static bool needsRehash(string $hashedValue, array $options = [])
 * @method static bool isHashed(string $value)
 * @method static string getDefaultDriver()
 * @method static mixed driver(string|null $driver = null)
 * @method static \QuantaQuirk\Hashing\HashManager extend(string $driver, \Closure $callback)
 * @method static array getDrivers()
 * @method static \QuantaQuirk\Contracts\Container\Container getContainer()
 * @method static \QuantaQuirk\Hashing\HashManager setContainer(\QuantaQuirk\Contracts\Container\Container $container)
 * @method static \QuantaQuirk\Hashing\HashManager forgetDrivers()
 *
 * @see \QuantaQuirk\Hashing\HashManager
 * @see \QuantaQuirk\Hashing\AbstractHasher
 */
class Hash extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'hash';
    }
}

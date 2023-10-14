<?php

namespace QuantaForge\Support\Facades;

/**
 * @method static \QuantaForge\Hashing\BcryptHasher createBcryptDriver()
 * @method static \QuantaForge\Hashing\ArgonHasher createArgonDriver()
 * @method static \QuantaForge\Hashing\Argon2IdHasher createArgon2idDriver()
 * @method static array info(string $hashedValue)
 * @method static string make(string $value, array $options = [])
 * @method static bool check(string $value, string $hashedValue, array $options = [])
 * @method static bool needsRehash(string $hashedValue, array $options = [])
 * @method static bool isHashed(string $value)
 * @method static string getDefaultDriver()
 * @method static mixed driver(string|null $driver = null)
 * @method static \QuantaForge\Hashing\HashManager extend(string $driver, \Closure $callback)
 * @method static array getDrivers()
 * @method static \QuantaForge\Contracts\Container\Container getContainer()
 * @method static \QuantaForge\Hashing\HashManager setContainer(\QuantaForge\Contracts\Container\Container $container)
 * @method static \QuantaForge\Hashing\HashManager forgetDrivers()
 *
 * @see \QuantaForge\Hashing\HashManager
 * @see \QuantaForge\Hashing\AbstractHasher
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

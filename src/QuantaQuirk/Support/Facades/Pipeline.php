<?php

namespace QuantaQuirk\Support\Facades;

/**
 * @method static \QuantaQuirk\Pipeline\Pipeline send(mixed $passable)
 * @method static \QuantaQuirk\Pipeline\Pipeline through(array|mixed $pipes)
 * @method static \QuantaQuirk\Pipeline\Pipeline pipe(array|mixed $pipes)
 * @method static \QuantaQuirk\Pipeline\Pipeline via(string $method)
 * @method static mixed then(\Closure $destination)
 * @method static mixed thenReturn()
 * @method static \QuantaQuirk\Pipeline\Pipeline setContainer(\QuantaQuirk\Contracts\Container\Container $container)
 *
 * @see \QuantaQuirk\Pipeline\Pipeline
 */
class Pipeline extends Facade
{
    /**
     * Indicates if the resolved instance should be cached.
     *
     * @var bool
     */
    protected static $cached = false;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pipeline';
    }
}

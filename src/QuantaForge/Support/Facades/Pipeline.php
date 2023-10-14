<?php

namespace QuantaForge\Support\Facades;

/**
 * @method static \QuantaForge\Pipeline\Pipeline send(mixed $passable)
 * @method static \QuantaForge\Pipeline\Pipeline through(array|mixed $pipes)
 * @method static \QuantaForge\Pipeline\Pipeline pipe(array|mixed $pipes)
 * @method static \QuantaForge\Pipeline\Pipeline via(string $method)
 * @method static mixed then(\Closure $destination)
 * @method static mixed thenReturn()
 * @method static \QuantaForge\Pipeline\Pipeline setContainer(\QuantaForge\Contracts\Container\Container $container)
 *
 * @see \QuantaForge\Pipeline\Pipeline
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

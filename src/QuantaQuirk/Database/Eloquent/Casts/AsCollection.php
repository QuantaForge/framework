<?php

namespace QuantaQuirk\Database\Eloquent\Casts;

use QuantaQuirk\Contracts\Database\Eloquent\Castable;
use QuantaQuirk\Contracts\Database\Eloquent\CastsAttributes;
use QuantaQuirk\Support\Collection;
use InvalidArgumentException;

class AsCollection implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return \QuantaQuirk\Contracts\Database\Eloquent\CastsAttributes<\QuantaQuirk\Support\Collection<array-key, mixed>, iterable>
     */
    public static function castUsing(array $arguments)
    {
        return new class($arguments) implements CastsAttributes
        {
            public function __construct(protected array $arguments)
            {
            }

            public function get($model, $key, $value, $attributes)
            {
                if (! isset($attributes[$key])) {
                    return;
                }

                $data = Json::decode($attributes[$key]);

                $collectionClass = $this->arguments[0] ?? Collection::class;

                if (! is_a($collectionClass, Collection::class, true)) {
                    throw new InvalidArgumentException('The provided class must extend ['.Collection::class.'].');
                }

                return is_array($data) ? new $collectionClass($data) : null;
            }

            public function set($model, $key, $value, $attributes)
            {
                return [$key => Json::encode($value)];
            }
        };
    }
}

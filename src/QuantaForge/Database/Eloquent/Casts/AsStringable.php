<?php

namespace QuantaForge\Database\Eloquent\Casts;

use QuantaForge\Contracts\Database\Eloquent\Castable;
use QuantaForge\Contracts\Database\Eloquent\CastsAttributes;
use QuantaForge\Support\Str;

class AsStringable implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return \QuantaForge\Contracts\Database\Eloquent\CastsAttributes<\QuantaForge\Support\Stringable, string|\Stringable>
     */
    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes
        {
            public function get($model, $key, $value, $attributes)
            {
                return isset($value) ? Str::of($value) : null;
            }

            public function set($model, $key, $value, $attributes)
            {
                return isset($value) ? (string) $value : null;
            }
        };
    }
}

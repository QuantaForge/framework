<?php

use function PHPStan\Testing\assertType;

assertType(
    'QuantaForge\Contracts\Database\Eloquent\CastsAttributes<QuantaForge\Database\Eloquent\Casts\ArrayObject<(int|string), mixed>, iterable>',
    \QuantaForge\Database\Eloquent\Casts\AsArrayObject::castUsing([]),
);

assertType(
    'QuantaForge\Contracts\Database\Eloquent\CastsAttributes<QuantaForge\Support\Collection<(int|string), mixed>, iterable>',
    \QuantaForge\Database\Eloquent\Casts\AsCollection::castUsing([]),
);

assertType(
    'QuantaForge\Contracts\Database\Eloquent\CastsAttributes<QuantaForge\Database\Eloquent\Casts\ArrayObject<(int|string), mixed>, iterable>',
    \QuantaForge\Database\Eloquent\Casts\AsEncryptedArrayObject::castUsing([]),
);

assertType(
    'QuantaForge\Contracts\Database\Eloquent\CastsAttributes<QuantaForge\Support\Collection<(int|string), mixed>, iterable>',
    \QuantaForge\Database\Eloquent\Casts\AsEncryptedCollection::castUsing([]),
);

assertType(
    'QuantaForge\Contracts\Database\Eloquent\CastsAttributes<QuantaForge\Database\Eloquent\Casts\ArrayObject<(int|string), UserType>, iterable<UserType>>',
    \QuantaForge\Database\Eloquent\Casts\AsEnumArrayObject::castUsing([\UserType::class]),
);

assertType(
    'QuantaForge\Contracts\Database\Eloquent\CastsAttributes<QuantaForge\Support\Collection<(int|string), UserType>, iterable<UserType>>',
    \QuantaForge\Database\Eloquent\Casts\AsEnumCollection::castUsing([\UserType::class]),
);

assertType(
    'QuantaForge\Contracts\Database\Eloquent\CastsAttributes<QuantaForge\Support\Stringable, string|Stringable>',
    \QuantaForge\Database\Eloquent\Casts\AsStringable::castUsing([]),
);

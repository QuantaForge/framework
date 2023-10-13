<?php

use function PHPStan\Testing\assertType;

assertType(
    'QuantaQuirk\Contracts\Database\Eloquent\CastsAttributes<QuantaQuirk\Database\Eloquent\Casts\ArrayObject<(int|string), mixed>, iterable>',
    \QuantaQuirk\Database\Eloquent\Casts\AsArrayObject::castUsing([]),
);

assertType(
    'QuantaQuirk\Contracts\Database\Eloquent\CastsAttributes<QuantaQuirk\Support\Collection<(int|string), mixed>, iterable>',
    \QuantaQuirk\Database\Eloquent\Casts\AsCollection::castUsing([]),
);

assertType(
    'QuantaQuirk\Contracts\Database\Eloquent\CastsAttributes<QuantaQuirk\Database\Eloquent\Casts\ArrayObject<(int|string), mixed>, iterable>',
    \QuantaQuirk\Database\Eloquent\Casts\AsEncryptedArrayObject::castUsing([]),
);

assertType(
    'QuantaQuirk\Contracts\Database\Eloquent\CastsAttributes<QuantaQuirk\Support\Collection<(int|string), mixed>, iterable>',
    \QuantaQuirk\Database\Eloquent\Casts\AsEncryptedCollection::castUsing([]),
);

assertType(
    'QuantaQuirk\Contracts\Database\Eloquent\CastsAttributes<QuantaQuirk\Database\Eloquent\Casts\ArrayObject<(int|string), UserType>, iterable<UserType>>',
    \QuantaQuirk\Database\Eloquent\Casts\AsEnumArrayObject::castUsing([\UserType::class]),
);

assertType(
    'QuantaQuirk\Contracts\Database\Eloquent\CastsAttributes<QuantaQuirk\Support\Collection<(int|string), UserType>, iterable<UserType>>',
    \QuantaQuirk\Database\Eloquent\Casts\AsEnumCollection::castUsing([\UserType::class]),
);

assertType(
    'QuantaQuirk\Contracts\Database\Eloquent\CastsAttributes<QuantaQuirk\Support\Stringable, string|Stringable>',
    \QuantaQuirk\Database\Eloquent\Casts\AsStringable::castUsing([]),
);

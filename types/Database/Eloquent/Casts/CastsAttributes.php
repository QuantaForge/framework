<?php

use function PHPStan\Testing\assertType;

/** @var User $user */
/** @var \QuantaForge\Contracts\Database\Eloquent\CastsAttributes<\QuantaForge\Support\Stringable, string|\Stringable> $cast */
assertType('QuantaForge\Support\Stringable|null', $cast->get($user, 'email', 'taylor@quantaforge.com', $user->getAttributes()));

$cast->set($user, 'email', 'taylor@quantaforge.com', $user->getAttributes()); // This works.
$cast->set($user, 'email', \QuantaForge\Support\Str::of('taylor@quantaforge.com'), $user->getAttributes()); // This also works!
$cast->set($user, 'email', null, $user->getAttributes()); // Also valid.

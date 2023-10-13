<?php

use function PHPStan\Testing\assertType;

/** @var User $user */
/** @var \QuantaQuirk\Contracts\Database\Eloquent\CastsAttributes<\QuantaQuirk\Support\Stringable, string|\Stringable> $cast */
assertType('QuantaQuirk\Support\Stringable|null', $cast->get($user, 'email', 'taylor@quantaquirk.com', $user->getAttributes()));

$cast->set($user, 'email', 'taylor@quantaquirk.com', $user->getAttributes()); // This works.
$cast->set($user, 'email', \QuantaQuirk\Support\Str::of('taylor@quantaquirk.com'), $user->getAttributes()); // This also works!
$cast->set($user, 'email', null, $user->getAttributes()); // Also valid.

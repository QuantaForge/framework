<?php

use function PHPStan\Testing\assertType;

$factory = User::factory();
assertType('QuantaQuirk\Database\Eloquent\Factories\Factory<User>', $factory);

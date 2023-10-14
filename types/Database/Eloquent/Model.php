<?php

use function PHPStan\Testing\assertType;

$factory = User::factory();
assertType('QuantaForge\Database\Eloquent\Factories\Factory<User>', $factory);

<?php

use QuantaForge\Support\Stringable;

use function PHPStan\Testing\assertType;

$stringable = new Stringable();

assertType('QuantaForge\Support\Collection<int, string>', $stringable->explode(''));

assertType('QuantaForge\Support\Collection<int, string>', $stringable->split(1));

assertType('QuantaForge\Support\Collection<int, string>', $stringable->ucsplit());

<?php

use QuantaQuirk\Support\Stringable;

use function PHPStan\Testing\assertType;

$stringable = new Stringable();

assertType('QuantaQuirk\Support\Collection<int, string>', $stringable->explode(''));

assertType('QuantaQuirk\Support\Collection<int, string>', $stringable->split(1));

assertType('QuantaQuirk\Support\Collection<int, string>', $stringable->ucsplit());

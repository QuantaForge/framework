<?php

use QuantaQuirk\Support\Timebox;

use function PHPStan\Testing\assertType;

assertType('int', (new Timebox)->call(function ($timebox) {
    assertType('QuantaQuirk\Support\Timebox', $timebox);

    return 1;
}, 1));

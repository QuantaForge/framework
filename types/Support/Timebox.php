<?php

use QuantaForge\Support\Timebox;

use function PHPStan\Testing\assertType;

assertType('int', (new Timebox)->call(function ($timebox) {
    assertType('QuantaForge\Support\Timebox', $timebox);

    return 1;
}, 1));

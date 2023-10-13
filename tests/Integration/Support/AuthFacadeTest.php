<?php

namespace QuantaQuirk\Tests\Integration\Support;

use QuantaQuirk\Support\Facades\Auth;
use Orchestra\Testbench\TestCase;
use RuntimeException;

class AuthFacadeTest extends TestCase
{
    public function testItFailsIfTheUiPackageIsMissing()
    {
        $this->expectExceptionObject(new RuntimeException(
            'In order to use the Auth::routes() method, please install the quantaquirk/ui package.'
        ));

        Auth::routes();
    }
}

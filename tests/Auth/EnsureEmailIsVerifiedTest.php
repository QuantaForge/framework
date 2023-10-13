<?php

namespace QuantaQuirk\Tests\Auth;

use QuantaQuirk\Auth\Middleware\EnsureEmailIsVerified;
use PHPUnit\Framework\TestCase;

class EnsureEmailIsVerifiedTest extends TestCase
{
    public function testItCanGenerateDefinitionViaStaticMethod()
    {
        $signature = (string) EnsureEmailIsVerified::redirectTo('route.name');
        $this->assertSame('QuantaQuirk\Auth\Middleware\EnsureEmailIsVerified:route.name', $signature);
    }
}

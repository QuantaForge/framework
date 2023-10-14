<?php

namespace QuantaForge\Tests\Auth;

use QuantaForge\Auth\Middleware\EnsureEmailIsVerified;
use PHPUnit\Framework\TestCase;

class EnsureEmailIsVerifiedTest extends TestCase
{
    public function testItCanGenerateDefinitionViaStaticMethod()
    {
        $signature = (string) EnsureEmailIsVerified::redirectTo('route.name');
        $this->assertSame('QuantaForge\Auth\Middleware\EnsureEmailIsVerified:route.name', $signature);
    }
}

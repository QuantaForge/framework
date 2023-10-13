<?php

namespace QuantaQuirk\Tests\Integration\Log;

use QuantaQuirk\Support\Facades\Log;
use Orchestra\Testbench\TestCase;

class LoggingIntegrationTest extends TestCase
{
    public function testLoggingCanBeRunWithoutEncounteringExceptions()
    {
        $this->expectNotToPerformAssertions();

        Log::info('Hello World');
    }
}

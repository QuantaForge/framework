<?php

namespace QuantaQuirk\Tests\Integration\Foundation;

use QuantaQuirk\Database\ConnectionResolverInterface;
use QuantaQuirk\Database\DatabaseManager;
use Orchestra\Testbench\TestCase;

class CoreContainerAliasesTest extends TestCase
{
    public function testItCanResolveCoreContainerAliases()
    {
        $this->assertInstanceOf(DatabaseManager::class, $this->app->make(ConnectionResolverInterface::class));
    }
}

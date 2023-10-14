<?php

namespace QuantaForge\Tests\Integration\Foundation;

use QuantaForge\Database\ConnectionResolverInterface;
use QuantaForge\Database\DatabaseManager;
use Orchestra\Testbench\TestCase;

class CoreContainerAliasesTest extends TestCase
{
    public function testItCanResolveCoreContainerAliases()
    {
        $this->assertInstanceOf(DatabaseManager::class, $this->app->make(ConnectionResolverInterface::class));
    }
}

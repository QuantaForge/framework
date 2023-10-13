<?php

namespace QuantaQuirk\Tests\Integration\Database\SqlServer;

use QuantaQuirk\Tests\Integration\Database\DatabaseTestCase;

abstract class SqlServerTestCase extends DatabaseTestCase
{
    protected function defineDatabaseMigrations()
    {
        if ($this->driver !== 'sqlsrv') {
            $this->markTestSkipped('Test requires a SQL Server connection.');
        }
    }
}

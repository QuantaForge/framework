<?php

namespace QuantaForge\Tests\Integration\Database\SqlServer;

use QuantaForge\Tests\Integration\Database\DatabaseTestCase;

abstract class SqlServerTestCase extends DatabaseTestCase
{
    protected function defineDatabaseMigrations()
    {
        if ($this->driver !== 'sqlsrv') {
            $this->markTestSkipped('Test requires a SQL Server connection.');
        }
    }
}

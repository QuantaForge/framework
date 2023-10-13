<?php

namespace QuantaQuirk\Tests\Integration\Database\Postgres;

use QuantaQuirk\Tests\Integration\Database\DatabaseTestCase;

abstract class PostgresTestCase extends DatabaseTestCase
{
    protected function defineDatabaseMigrations()
    {
        if ($this->driver !== 'pgsql') {
            $this->markTestSkipped('Test requires a PostgreSQL connection.');
        }
    }
}

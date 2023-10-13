<?php

namespace QuantaQuirk\Tests\Integration\Database;

use QuantaQuirk\Foundation\Testing\DatabaseMigrations;

class EloquentTransactionWithAfterCommitUsingDatabaseMigrationsTest extends DatabaseTestCase
{
    use EloquentTransactionWithAfterCommitTests;
    use DatabaseMigrations;
}

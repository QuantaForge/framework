<?php

namespace QuantaQuirk\Tests\Database;

use QuantaQuirk\Database\Connection;
use QuantaQuirk\Database\Schema\Grammars\Grammar;
use LogicException;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class DatabaseAbstractSchemaGrammarTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testCreateDatabase()
    {
        $grammar = new class extends Grammar {};

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('This database driver does not support creating databases.');

        $grammar->compileCreateDatabase('foo', m::mock(Connection::class));
    }

    public function testDropDatabaseIfExists()
    {
        $grammar = new class extends Grammar {};

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('This database driver does not support dropping databases.');

        $grammar->compileDropDatabaseIfExists('foo');
    }
}

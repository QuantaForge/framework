<?php

namespace QuantaQuirk\Tests\Database;

use QuantaQuirk\Container\Container;
use QuantaQuirk\Database\Connection;
use QuantaQuirk\Database\Schema\SQLiteBuilder;
use QuantaQuirk\Filesystem\Filesystem;
use QuantaQuirk\Support\Facades\Facade;
use QuantaQuirk\Support\Facades\File;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class DatabaseSQLiteBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        $app = new Container;

        Container::setInstance($app)
            ->singleton('files', Filesystem::class);

        Facade::setFacadeApplication($app);
    }

    protected function tearDown(): void
    {
        m::close();

        Container::setInstance(null);
        Facade::setFacadeApplication(null);
    }

    public function testCreateDatabase()
    {
        $connection = m::mock(Connection::class);
        $connection->shouldReceive('getSchemaGrammar')->once();

        $builder = new SQLiteBuilder($connection);

        File::shouldReceive('put')
            ->once()
            ->with('my_temporary_database_a', '')
            ->andReturn(20); // bytes

        $this->assertTrue($builder->createDatabase('my_temporary_database_a'));

        File::shouldReceive('put')
            ->once()
            ->with('my_temporary_database_b', '')
            ->andReturn(false);

        $this->assertFalse($builder->createDatabase('my_temporary_database_b'));
    }

    public function testDropDatabaseIfExists()
    {
        $connection = m::mock(Connection::class);
        $connection->shouldReceive('getSchemaGrammar')->once();

        $builder = new SQLiteBuilder($connection);

        File::shouldReceive('exists')
            ->once()
            ->andReturn(true);

        File::shouldReceive('delete')
            ->once()
            ->with('my_temporary_database_b')
            ->andReturn(true);

        $this->assertTrue($builder->dropDatabaseIfExists('my_temporary_database_b'));

        File::shouldReceive('exists')
            ->once()
            ->andReturn(false);

        $this->assertTrue($builder->dropDatabaseIfExists('my_temporary_database_c'));

        File::shouldReceive('exists')
            ->once()
            ->andReturn(true);

        File::shouldReceive('delete')
            ->once()
            ->with('my_temporary_database_c')
            ->andReturn(false);

        $this->assertFalse($builder->dropDatabaseIfExists('my_temporary_database_c'));
    }
}

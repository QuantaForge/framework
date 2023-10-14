<?php

namespace QuantaForge\Tests\Database;

use QuantaForge\Database\Console\Migrations\MigrateMakeCommand;
use QuantaForge\Database\Migrations\MigrationCreator;
use QuantaForge\Foundation\Application;
use QuantaForge\Support\Composer;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class DatabaseMigrationMakeCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testBasicCreateDumpsAutoload()
    {
        $command = new MigrateMakeCommand(
            $creator = m::mock(MigrationCreator::class),
            $composer = m::mock(Composer::class)
        );
        $app = new Application;
        $app->useDatabasePath(__DIR__);
        $command->setQuantaForge($app);
        $creator->shouldReceive('create')->once()
                ->with('create_foo', __DIR__.DIRECTORY_SEPARATOR.'migrations', 'foo', true)
                ->andReturn(__DIR__.'/migrations/2021_04_23_110457_create_foo.php');

        $this->runCommand($command, ['name' => 'create_foo']);
    }

    public function testBasicCreateGivesCreatorProperArguments()
    {
        $command = new MigrateMakeCommand(
            $creator = m::mock(MigrationCreator::class),
            m::mock(Composer::class)->shouldIgnoreMissing()
        );
        $app = new Application;
        $app->useDatabasePath(__DIR__);
        $command->setQuantaForge($app);
        $creator->shouldReceive('create')->once()
                ->with('create_foo', __DIR__.DIRECTORY_SEPARATOR.'migrations', 'foo', true)
                ->andReturn(__DIR__.'/migrations/2021_04_23_110457_create_foo.php');

        $this->runCommand($command, ['name' => 'create_foo']);
    }

    public function testBasicCreateGivesCreatorProperArgumentsWhenNameIsStudlyCase()
    {
        $command = new MigrateMakeCommand(
            $creator = m::mock(MigrationCreator::class),
            m::mock(Composer::class)->shouldIgnoreMissing()
        );
        $app = new Application;
        $app->useDatabasePath(__DIR__);
        $command->setQuantaForge($app);
        $creator->shouldReceive('create')->once()
                ->with('create_foo', __DIR__.DIRECTORY_SEPARATOR.'migrations', 'foo', true)
                ->andReturn(__DIR__.'/migrations/2021_04_23_110457_create_foo.php');

        $this->runCommand($command, ['name' => 'CreateFoo']);
    }

    public function testBasicCreateGivesCreatorProperArgumentsWhenTableIsSet()
    {
        $command = new MigrateMakeCommand(
            $creator = m::mock(MigrationCreator::class),
            m::mock(Composer::class)->shouldIgnoreMissing()
        );
        $app = new Application;
        $app->useDatabasePath(__DIR__);
        $command->setQuantaForge($app);
        $creator->shouldReceive('create')->once()
                ->with('create_foo', __DIR__.DIRECTORY_SEPARATOR.'migrations', 'users', true)
                ->andReturn(__DIR__.'/migrations/2021_04_23_110457_create_foo.php');

        $this->runCommand($command, ['name' => 'create_foo', '--create' => 'users']);
    }

    public function testBasicCreateGivesCreatorProperArgumentsWhenCreateTablePatternIsFound()
    {
        $command = new MigrateMakeCommand(
            $creator = m::mock(MigrationCreator::class),
            m::mock(Composer::class)->shouldIgnoreMissing()
        );
        $app = new Application;
        $app->useDatabasePath(__DIR__);
        $command->setQuantaForge($app);
        $creator->shouldReceive('create')->once()
                ->with('create_users_table', __DIR__.DIRECTORY_SEPARATOR.'migrations', 'users', true)
                ->andReturn(__DIR__.'/migrations/2021_04_23_110457_create_users_table.php');

        $this->runCommand($command, ['name' => 'create_users_table']);
    }

    public function testCanSpecifyPathToCreateMigrationsIn()
    {
        $command = new MigrateMakeCommand(
            $creator = m::mock(MigrationCreator::class),
            m::mock(Composer::class)->shouldIgnoreMissing()
        );
        $app = new Application;
        $command->setQuantaForge($app);
        $app->setBasePath('/home/quantaforge');
        $creator->shouldReceive('create')->once()
                ->with('create_foo', '/home/quantaforge/vendor/quantaforge-package/migrations', 'users', true)
                ->andReturn('/home/quantaforge/vendor/quantaforge-package/migrations/2021_04_23_110457_create_foo.php');
        $this->runCommand($command, ['name' => 'create_foo', '--path' => 'vendor/quantaforge-package/migrations', '--create' => 'users']);
    }

    protected function runCommand($command, $input = [])
    {
        return $command->run(new ArrayInput($input), new NullOutput);
    }
}

<?php

namespace QuantaQuirk\Console;

use QuantaQuirk\Filesystem\Filesystem;
use QuantaQuirk\Support\Composer;

abstract class MigrationGeneratorCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \QuantaQuirk\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The Composer instance.
     *
     * @var \QuantaQuirk\Support\Composer
     *
     * @deprecated Will be removed in a future QuantaQuirk version.
     */
    protected $composer;

    /**
     * Create a new migration generator command instance.
     *
     * @param  \QuantaQuirk\Filesystem\Filesystem  $files
     * @param  \QuantaQuirk\Support\Composer  $composer
     * @return void
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * Get the migration table name.
     *
     * @return string
     */
    abstract protected function migrationTableName();

    /**
     * Get the path to the migration stub file.
     *
     * @return string
     */
    abstract protected function migrationStubFile();

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $table = $this->migrationTableName();

        if ($this->migrationExists($table)) {
            $this->components->error('Migration already exists.');

            return 1;
        }

        $this->replaceMigrationPlaceholders(
            $this->createBaseMigration($table), $table
        );

        $this->components->info('Migration created successfully.');

        return 0;
    }

    /**
     * Create a base migration file for the table.
     *
     * @param  string  $table
     * @return string
     */
    protected function createBaseMigration($table)
    {
        return $this->quantaquirk['migration.creator']->create(
            'create_'.$table.'_table', $this->quantaquirk->databasePath('/migrations')
        );
    }

    /**
     * Replace the placeholders in the generated migration file.
     *
     * @param  string  $path
     * @param  string  $table
     * @return void
     */
    protected function replaceMigrationPlaceholders($path, $table)
    {
        $stub = str_replace(
            '{{table}}', $table, $this->files->get($this->migrationStubFile())
        );

        $this->files->put($path, $stub);
    }

    /**
     * Determine whether a migration for the table already exists.
     *
     * @param  string  $table
     * @return bool
     */
    protected function migrationExists($table)
    {
        return count($this->files->glob(
            $this->quantaquirk->joinPaths($this->quantaquirk->databasePath('migrations'), '*_*_*_*_create_'.$table.'_table.php')
        )) !== 0;
    }
}

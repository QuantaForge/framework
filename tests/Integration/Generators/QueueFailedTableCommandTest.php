<?php

namespace QuantaForge\Tests\Integration\Generators;

class QueueFailedTableCommandTest extends TestCase
{
    public function testCreateMakesMigration()
    {
        $this->artisan('queue:failed-table')->assertExitCode(0);

        $this->assertMigrationFileContains([
            'use QuantaForge\Database\Migrations\Migration;',
            'return new class extends Migration',
            'Schema::create(\'failed_jobs\', function (Blueprint $table) {',
            'Schema::dropIfExists(\'failed_jobs\');',
        ], 'create_failed_jobs_table.php');
    }
}

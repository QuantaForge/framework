<?php

namespace QuantaQuirk\Tests\Integration\Generators;

class QueueTableCommandTest extends TestCase
{
    public function testCreateMakesMigration()
    {
        $this->artisan('queue:table')->assertExitCode(0);

        $this->assertMigrationFileContains([
            'use QuantaQuirk\Database\Migrations\Migration;',
            'return new class extends Migration',
            'Schema::create(\'jobs\', function (Blueprint $table) {',
            'Schema::dropIfExists(\'jobs\');',
        ], 'create_jobs_table.php');
    }
}

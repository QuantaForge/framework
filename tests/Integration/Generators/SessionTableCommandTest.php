<?php

namespace QuantaForge\Tests\Integration\Generators;

class SessionTableCommandTest extends TestCase
{
    public function testCreateMakesMigration()
    {
        $this->artisan('session:table')->assertExitCode(0);

        $this->assertMigrationFileContains([
            'use QuantaForge\Database\Migrations\Migration;',
            'return new class extends Migration',
            'Schema::create(\'sessions\', function (Blueprint $table) {',
            'Schema::dropIfExists(\'sessions\');',
        ], 'create_sessions_table.php');
    }
}

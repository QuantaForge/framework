<?php

namespace QuantaQuirk\Tests\Integration\Generators;

class NotificationTableCommandTest extends TestCase
{
    public function testCreateMakesMigration()
    {
        $this->artisan('notifications:table')->assertExitCode(0);

        $this->assertMigrationFileContains([
            'use QuantaQuirk\Database\Migrations\Migration;',
            'return new class extends Migration',
            'Schema::create(\'notifications\', function (Blueprint $table) {',
            'Schema::dropIfExists(\'notifications\');',
        ], 'create_notifications_table.php');
    }
}

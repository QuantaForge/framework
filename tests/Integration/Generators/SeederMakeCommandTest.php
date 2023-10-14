<?php

namespace QuantaForge\Tests\Integration\Generators;

class SeederMakeCommandTest extends TestCase
{
    protected $files = [
        'database/seeders/FooSeeder.php',
    ];

    public function testItCanGenerateSeederFile()
    {
        $this->artisan('make:seeder', ['name' => 'FooSeeder'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace Database\Seeders;',
            'use QuantaForge\Database\Seeder;',
            'class FooSeeder extends Seeder',
            'public function run()',
        ], 'database/seeders/FooSeeder.php');
    }
}

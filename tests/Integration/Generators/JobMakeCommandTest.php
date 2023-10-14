<?php

namespace QuantaForge\Tests\Integration\Generators;

class JobMakeCommandTest extends TestCase
{
    protected $files = [
        'app/Jobs/FooCreated.php',
        'tests/Feature/Jobs/FooCreatedTest.php',
    ];

    public function testItCanGenerateJobFile()
    {
        $this->artisan('make:job', ['name' => 'FooCreated'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Jobs;',
            'use QuantaForge\Bus\Queueable;',
            'use QuantaForge\Contracts\Queue\ShouldQueue;',
            'use QuantaForge\Foundation\Bus\Dispatchable;',
            'use QuantaForge\Queue\InteractsWithQueue;',
            'use QuantaForge\Queue\SerializesModels;',
            'class FooCreated implements ShouldQueue',
        ], 'app/Jobs/FooCreated.php');

        $this->assertFilenameNotExists('tests/Feature/Jobs/FooCreatedTest.php');
    }

    public function testItCanGenerateSyncJobFile()
    {
        $this->artisan('make:job', ['name' => 'FooCreated', '--sync' => true])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Jobs;',
            'use QuantaForge\Foundation\Bus\Dispatchable;',
            'class FooCreated',
        ], 'app/Jobs/FooCreated.php');

        $this->assertFileNotContains([
            'use QuantaForge\Contracts\Queue\ShouldQueue;',
            'use QuantaForge\Queue\InteractsWithQueue;',
            'use QuantaForge\Queue\SerializesModels;',
        ], 'app/Jobs/FooCreated.php');
    }

    public function testItCanGenerateJobFileWithTest()
    {
        $this->artisan('make:job', ['name' => 'FooCreated', '--test' => true])
            ->assertExitCode(0);

        $this->assertFilenameExists('app/Jobs/FooCreated.php');
        $this->assertFilenameExists('tests/Feature/Jobs/FooCreatedTest.php');
    }
}

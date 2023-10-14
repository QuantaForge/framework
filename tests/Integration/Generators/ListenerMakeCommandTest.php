<?php

namespace QuantaForge\Tests\Integration\Generators;

class ListenerMakeCommandTest extends TestCase
{
    protected $files = [
        'app/Listeners/FooListener.php',
        'tests/Feature/Listeners/FooListenerTest.php',
    ];

    public function testItCanGenerateListenerFile()
    {
        $this->artisan('make:listener', ['name' => 'FooListener'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Listeners;',
            'class FooListener',
            'public function handle(object $event)',
        ], 'app/Listeners/FooListener.php');

        $this->assertFileNotContains([
            'class FooListener implements ShouldQueue',
        ], 'app/Listeners/FooListener.php');
    }

    public function testItCanGenerateListenerFileForEvent()
    {
        $this->artisan('make:listener', ['name' => 'FooListener', '--event' => 'FooListenerCreated'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Listeners;',
            'use App\Events\FooListenerCreated;',
            'class FooListener',
            'public function handle(FooListenerCreated $event)',
        ], 'app/Listeners/FooListener.php');
    }

    public function testItCanGenerateListenerFileForQuantaForgeEvent()
    {
        $this->artisan('make:listener', ['name' => 'FooListener', '--event' => 'QuantaForge\Auth\Events\Login'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Listeners;',
            'use QuantaForge\Auth\Events\Login;',
            'class FooListener',
            'public function handle(Login $event)',
        ], 'app/Listeners/FooListener.php');
    }

    public function testItCanGenerateQueuedListenerFile()
    {
        $this->artisan('make:listener', ['name' => 'FooListener', '--queued' => true])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Listeners;',
            'use QuantaForge\Contracts\Queue\ShouldQueue;',
            'use QuantaForge\Queue\InteractsWithQueue;',
            'class FooListener implements ShouldQueue',
            'public function handle(object $event)',
        ], 'app/Listeners/FooListener.php');
    }

    public function testItCanGenerateQueuedListenerFileForEvent()
    {
        $this->artisan('make:listener', ['name' => 'FooListener', '--queued' => true, '--event' => 'FooListenerCreated'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Listeners;',
            'use App\Events\FooListenerCreated;',
            'use QuantaForge\Contracts\Queue\ShouldQueue;',
            'use QuantaForge\Queue\InteractsWithQueue;',
            'class FooListener implements ShouldQueue',
            'public function handle(FooListenerCreated $event)',
        ], 'app/Listeners/FooListener.php');
    }

    public function testItCanGenerateQueuedListenerFileForQuantaForgeEvent()
    {
        $this->artisan('make:listener', ['name' => 'FooListener', '--queued' => true, '--event' => 'QuantaForge\Auth\Events\Login'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Listeners;',
            'use QuantaForge\Auth\Events\Login;',
            'use QuantaForge\Contracts\Queue\ShouldQueue;',
            'use QuantaForge\Queue\InteractsWithQueue;',
            'class FooListener implements ShouldQueue',
            'public function handle(Login $event)',
        ], 'app/Listeners/FooListener.php');
    }

    public function testItCanGenerateQueuedListenerFileWithTest()
    {
        $this->artisan('make:listener', ['name' => 'FooListener', '--test' => true])
            ->assertExitCode(0);

        $this->assertFilenameExists('app/Listeners/FooListener.php');
        $this->assertFilenameExists('tests/Feature/Listeners/FooListenerTest.php');
    }
}

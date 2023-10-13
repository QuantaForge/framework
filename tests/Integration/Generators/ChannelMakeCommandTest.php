<?php

namespace QuantaQuirk\Tests\Integration\Generators;

class ChannelMakeCommandTest extends TestCase
{
    protected $files = [
        'app/Broadcasting/FooChannel.php',
    ];

    public function testItCanGenerateChannelFile()
    {
        $this->artisan('make:channel', ['name' => 'FooChannel'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Broadcasting;',
            'use QuantaQuirk\Foundation\Auth\User;',
            'class FooChannel',
        ], 'app/Broadcasting/FooChannel.php');
    }
}

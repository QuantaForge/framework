<?php

namespace QuantaForge\Tests\Integration\Generators;

class ResourceMakeCommandTest extends TestCase
{
    protected $files = [
        'app/Http/Resources/FooResource.php',
        'app/Http/Resources/FooResourceCollection.php',
    ];

    /** @test */
    public function it_can_generate_resource_file()
    {
        $this->artisan('make:resource', ['name' => 'FooResource'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Http\Resources;',
            'use QuantaForge\Http\Resources\Json\JsonResource;',
            'class FooResource extends JsonResource',
        ], 'app/Http/Resources/FooResource.php');
    }

    /** @test */
    public function it_can_generate_resource_collection_file()
    {
        $this->artisan('make:resource', ['name' => 'FooResourceCollection', '--collection' => true])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Http\Resources;',
            'use QuantaForge\Http\Resources\Json\ResourceCollection;',
            'class FooResourceCollection extends ResourceCollection',
        ], 'app/Http/Resources/FooResourceCollection.php');
    }
}

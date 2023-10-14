<?php

namespace QuantaForge\Tests\Integration\Testing;

use QuantaForge\Support\Facades\Vite;
use Orchestra\Testbench\TestCase;

class TestCaseTest extends TestCase
{
    public function test_without_vite_clear_facade_resolved_instance()
    {
        Vite::useScriptTagAttributes([
            'crossorigin' => 'anonymous',
        ]);

        $this->withoutVite();

        Vite::asset('foo.png');
    }
}

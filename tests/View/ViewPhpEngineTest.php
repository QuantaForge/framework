<?php

namespace QuantaQuirk\Tests\View;

use QuantaQuirk\Filesystem\Filesystem;
use QuantaQuirk\View\Engines\PhpEngine;
use PHPUnit\Framework\TestCase;

class ViewPhpEngineTest extends TestCase
{
    public function testViewsMayBeProperlyRendered()
    {
        $engine = new PhpEngine(new Filesystem);
        $this->assertSame('Hello World
', $engine->get(__DIR__.'/fixtures/basic.php'));
    }
}

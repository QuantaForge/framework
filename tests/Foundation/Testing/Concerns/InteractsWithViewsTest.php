<?php

namespace QuantaQuirk\Tests\Foundation\Testing\Concerns;

use QuantaQuirk\Foundation\Testing\Concerns\InteractsWithViews;
use QuantaQuirk\View\Component;
use Orchestra\Testbench\TestCase;

class InteractsWithViewsTest extends TestCase
{
    use InteractsWithViews;

    public function testBladeCorrectlyRendersString()
    {
        $string = (string) $this->blade('@if(true)test @endif');

        $this->assertSame('test ', $string);
    }

    public function testComponentCanAccessPublicProperties()
    {
        $exampleComponent = new class extends Component
        {
            public $foo = 'bar';

            public function speak()
            {
                return 'hello';
            }

            public function render()
            {
                return 'rendered content';
            }
        };

        $component = $this->component(get_class($exampleComponent));

        $this->assertSame('bar', $component->foo);
        $this->assertSame('hello', $component->speak());
        $component->assertSee('content');
    }
}

<?php

namespace QuantaQuirk\Tests\Integration\Generators;

use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use InteractsWithPublishedFiles;
}

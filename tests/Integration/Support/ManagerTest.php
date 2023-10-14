<?php

namespace QuantaForge\Tests\Integration\Support;

use QuantaForge\Tests\Integration\Support\Fixtures\NullableManager;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;

class ManagerTest extends TestCase
{
    public function testDefaultDriverCannotBeNull()
    {
        $this->expectException(InvalidArgumentException::class);

        (new NullableManager($this->app))->driver();
    }
}

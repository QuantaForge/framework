<?php

namespace QuantaQuirk\Tests\Database;

use QuantaQuirk\Database\Concerns\BuildsQueries;
use PHPUnit\Framework\TestCase;

class DatabaseConcernsBuildsQueriesTraitTest extends TestCase
{
    public function testTapCallbackInstance()
    {
        $mock = $this->getMockForTrait(BuildsQueries::class);
        $mock->tap(function ($builder) use ($mock) {
            $this->assertEquals($mock, $builder);
        });
    }
}

<?php

namespace QuantaForge\Tests\Database;

use QuantaForge\Database\Eloquent\Casts\Attribute;
use QuantaForge\Database\Eloquent\Concerns\HasAttributes;
use PHPUnit\Framework\TestCase;

class DatabaseConcernsHasAttributesTest extends TestCase
{
    public function testWithoutConstructor()
    {
        $instance = new HasAttributesWithoutConstructor();
        $attributes = $instance->getMutatedAttributes();
        $this->assertEquals(['some_attribute'], $attributes);
    }

    public function testWithConstructorArguments()
    {
        $instance = new HasAttributesWithConstructorArguments(null);
        $attributes = $instance->getMutatedAttributes();
        $this->assertEquals(['some_attribute'], $attributes);
    }
}

class HasAttributesWithoutConstructor
{
    use HasAttributes;

    public function someAttribute(): Attribute
    {
        return new Attribute(function () {
        });
    }
}

class HasAttributesWithConstructorArguments extends HasAttributesWithoutConstructor
{
    public function __construct($someValue)
    {
    }
}

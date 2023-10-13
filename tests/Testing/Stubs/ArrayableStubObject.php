<?php

namespace QuantaQuirk\Tests\Testing\Stubs;

use QuantaQuirk\Contracts\Support\Arrayable;

class ArrayableStubObject implements Arrayable
{
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public static function make($data = [])
    {
        return new self($data);
    }

    public function toArray()
    {
        return $this->data;
    }
}

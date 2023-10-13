<?php

namespace QuantaQuirk\Tests\Validation\fixtures;

use QuantaQuirk\Contracts\Support\Arrayable;

class Values implements Arrayable
{
    public function toArray()
    {
        return [1, 2, 3, 4];
    }
}

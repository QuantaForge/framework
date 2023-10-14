<?php

namespace QuantaForge\Tests\Validation\fixtures;

use QuantaForge\Contracts\Support\Arrayable;

class Values implements Arrayable
{
    public function toArray()
    {
        return [1, 2, 3, 4];
    }
}

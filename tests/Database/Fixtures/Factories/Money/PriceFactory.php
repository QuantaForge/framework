<?php

namespace QuantaQuirk\Tests\Database\Fixtures\Factories\Money;

use QuantaQuirk\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}

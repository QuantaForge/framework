<?php

namespace QuantaForge\Tests\Database\Fixtures\Factories\Money;

use QuantaForge\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}

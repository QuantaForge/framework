<?php

namespace QuantaForge\Tests\Database\Fixtures\Models\Money;

use QuantaForge\Database\Eloquent\Factories\HasFactory;
use QuantaForge\Database\Eloquent\Model;
use QuantaForge\Tests\Database\Fixtures\Factories\Money\PriceFactory;

class Price extends Model
{
    use HasFactory;

    protected $table = 'prices';

    public static function factory()
    {
        return PriceFactory::new();
    }
}

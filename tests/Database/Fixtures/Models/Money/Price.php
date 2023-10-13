<?php

namespace QuantaQuirk\Tests\Database\Fixtures\Models\Money;

use QuantaQuirk\Database\Eloquent\Factories\HasFactory;
use QuantaQuirk\Database\Eloquent\Model;
use QuantaQuirk\Tests\Database\Fixtures\Factories\Money\PriceFactory;

class Price extends Model
{
    use HasFactory;

    protected $table = 'prices';

    public static function factory()
    {
        return PriceFactory::new();
    }
}

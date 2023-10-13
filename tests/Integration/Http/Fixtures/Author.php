<?php

namespace QuantaQuirk\Tests\Integration\Http\Fixtures;

use QuantaQuirk\Database\Eloquent\Model;

class Author extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}

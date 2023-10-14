<?php

namespace QuantaForge\Tests\Integration\Http\Fixtures;

use QuantaForge\Database\Eloquent\Model;

class Author extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}

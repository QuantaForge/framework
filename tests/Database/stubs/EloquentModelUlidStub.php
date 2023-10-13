<?php

use QuantaQuirk\Database\Eloquent\Concerns\HasUlids;
use QuantaQuirk\Database\Eloquent\Model;

class EloquentModelUlidStub extends Model
{
    use HasUlids;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'model';
}

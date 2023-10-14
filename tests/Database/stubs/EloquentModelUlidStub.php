<?php

use QuantaForge\Database\Eloquent\Concerns\HasUlids;
use QuantaForge\Database\Eloquent\Model;

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

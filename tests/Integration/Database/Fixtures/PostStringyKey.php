<?php

namespace QuantaForge\Tests\Integration\Database\Fixtures;

use QuantaForge\Database\Eloquent\Model;

class PostStringyKey extends Model
{
    public $table = 'my_posts';

    public $primaryKey = 'my_id';
}

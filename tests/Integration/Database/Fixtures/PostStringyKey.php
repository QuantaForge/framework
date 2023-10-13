<?php

namespace QuantaQuirk\Tests\Integration\Database\Fixtures;

use QuantaQuirk\Database\Eloquent\Model;

class PostStringyKey extends Model
{
    public $table = 'my_posts';

    public $primaryKey = 'my_id';
}

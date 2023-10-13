<?php

namespace QuantaQuirk\Tests\Integration\Auth\Fixtures\Models;

use QuantaQuirk\Foundation\Auth\User as Authenticatable;

class AuthenticationTestUser extends Authenticatable
{
    public $table = 'users';
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var string[]
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}

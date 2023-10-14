<?php

namespace QuantaForge\Tests\Integration\Auth\Fixtures;

use QuantaForge\Foundation\Auth\User as Authenticatable;
use QuantaForge\Notifications\Notifiable;

class AuthenticationTestUser extends Authenticatable
{
    use Notifiable;

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

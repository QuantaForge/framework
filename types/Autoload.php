<?php

use QuantaQuirk\Database\Eloquent\Factories\HasFactory;
use QuantaQuirk\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
}

enum UserType
{
}

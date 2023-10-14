<?php

use QuantaForge\Database\Eloquent\Factories\HasFactory;
use QuantaForge\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
}

enum UserType
{
}

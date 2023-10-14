<?php

namespace QuantaForge\Foundation\Auth;

use QuantaForge\Auth\Authenticatable;
use QuantaForge\Auth\MustVerifyEmail;
use QuantaForge\Auth\Passwords\CanResetPassword;
use QuantaForge\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use QuantaForge\Contracts\Auth\Authenticatable as AuthenticatableContract;
use QuantaForge\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use QuantaForge\Database\Eloquent\Model;
use QuantaForge\Foundation\Auth\Access\Authorizable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
}

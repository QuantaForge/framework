<?php

namespace QuantaQuirk\Foundation\Auth;

use QuantaQuirk\Auth\Authenticatable;
use QuantaQuirk\Auth\MustVerifyEmail;
use QuantaQuirk\Auth\Passwords\CanResetPassword;
use QuantaQuirk\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use QuantaQuirk\Contracts\Auth\Authenticatable as AuthenticatableContract;
use QuantaQuirk\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use QuantaQuirk\Database\Eloquent\Model;
use QuantaQuirk\Foundation\Auth\Access\Authorizable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
}

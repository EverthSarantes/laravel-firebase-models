<?php
namespace Firebase\Auth\Models;

use Firebase\Models\FirebaseModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Firebase\Auth\Traits\FirebaseHasApiTokens;

class User extends FirebaseModel implements Authenticatable
{
    use FirebaseHasApiTokens;
    protected $collection = 'users';

    //Model Atributes
    // username
    // password

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->attributes['id'];
    }

    public function getAuthPassword()
    {
        return $this->attributes['password'];
    }

    public function getRememberToken()
    {
        return $this->attributes['remember_token'] ?? null;
    }

    public function setRememberToken($value)
    {
        $this->attributes['remember_token'] = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function getAuthPasswordName()
    {
        return 'password';
    }

    public function getKeyName()
    {
        return 'id';
    }
}
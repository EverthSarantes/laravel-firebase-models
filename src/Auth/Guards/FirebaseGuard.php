<?php

namespace Firebase\Auth\Guards;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Firebase\Auth\Models\User;
use Firebase\Auth\Providers\FirebaseUserProvider;
use Illuminate\Support\Facades\Hash;

class FirebaseGuard implements Guard
{
    protected $user;

    public function hasUser()
    {
        return !is_null($this->user);
    }

    public function check()
    {
        return $this->user() !== null;
    }

    public function guest()
    {
        return !$this->check();
    }

    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        $userId = session()->get('firebase_user_id');

        if ($userId) {
            $this->user = User::find($userId);
        }

        return $this->user;
    }

    public function id()
    {
        return $this->user() ? $this->user()->id : null;
    }

    public function validate(array $credentials = [])
    {
        $firebase_user = User::where('username', $credentials['username'])->first(); 

        if (!$firebase_user || !Hash::check($credentials['password'], $firebase_user->password)) {
            return false;
        }

        $this->setUser($firebase_user);

        session()->put('firebase_user_id', $firebase_user->id);

        return true;
    }

    public function attempt(array $credentials = [])
    {
        return $this->validate($credentials);
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        return $this;
    }

    public function logout()
    {
        $this->user = null;
        session()->forget('firebase_user_id');
    }

    public function getProvider()
    {
        return new FirebaseUserProvider();
    }
}
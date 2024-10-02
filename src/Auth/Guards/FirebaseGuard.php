<?php

namespace Firebase\Auth\Guards;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use App\Models\Firebase\User;

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

        // Recuperar el ID del usuario desde la sesión
        $userId = session()->get('firebase_user_id');

        if ($userId) {
            // Buscar al usuario en Firebase usando el ID
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
        $firebase_user = User::where('username', $credentials['username'])->first(); // Usa first para buscar el primer registro

        if (!$firebase_user || $firebase_user->password !== $credentials['password']) {
            return false;
        }

        // Si la validación es exitosa, guardar el usuario autenticado
        $this->setUser($firebase_user);

        // Guardar el ID del usuario en la sesión
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
}
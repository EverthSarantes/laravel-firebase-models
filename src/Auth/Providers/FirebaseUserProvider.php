<?php

namespace Firebase\Auth\Providers;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\Firebase\User;


class FirebaseUserProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        return User::find($identifier);
    }

    public function retrieveByCredentials(array $credentials)
    {
        return User::where('username', $credentials['username'])[0];
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $user->password === $credentials['password'];
    }

    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        return null;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false): void
    {
        // Implementation needed or remove the method if not required
    }
}
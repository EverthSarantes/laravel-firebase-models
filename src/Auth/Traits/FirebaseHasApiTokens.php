<?php

namespace Firebase\Auth\Traits;

use Illuminate\Support\Facades\DB;

trait FirebaseHasApiTokens
{
    public function tokens()
    {
        return DB::table('personal_access_tokens')
        ->where('tokenable_type', self::class)
        ->where('tokenable_id', $this->id)
        ->get();
    }

    public function revokeToken($tokenId)
    {
        DB::table('personal_access_tokens')
        ->where('id', $tokenId)
        ->delete();
    }

    public function revokeAllTokens()
    {
        DB::table('personal_access_tokens')
        ->where('tokenable_type', self::class)
        ->where('tokenable_id', $this->id)
        ->delete();
    }

    public function createToken(string $name, array $abilities = ['*'])
    {
        $plainTextToken = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $plainTextToken);

        DB::table('personal_access_tokens')->insert([
            'name' => $name,
            'tokenable_type' => self::class,
            'tokenable_id' => $this->id,
            'token' => $hashedToken,
            'abilities' => json_encode($abilities),
            'last_used_at' => null,
            'expires_at' => now()->addWeek(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $plainTextToken;
    }

    public function getConnectionName()
    {
        return null;
    }

    public function setConnection()
    {
        return null;
    }

    public function newQuery()
    {
        return null;
    }
}
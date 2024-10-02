<?php

namespace Firebase\Auth\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthenticateWithFirebaseTokens
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if ($token) {
            $personalAccessToken = DB::table('personal_access_tokens')
                ->where('token', hash('sha256', $token))
                ->first();

            if ($personalAccessToken) {
                $user = Auth::guard('firebase')->getProvider()->retrieveById($personalAccessToken->tokenable_id);
                if ($user) {
                    Auth::guard('firebase')->setUser($user);
                    return $next($request);
                }
            }
        }

        return response()->json(['message' => 'Unauthenticated'], 401);
    }
}
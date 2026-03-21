<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class TokenFromQuery
{
    public function handle(Request $request, Closure $next)
    {
        // If already authenticated via session, continue
        if (auth()->check()) {
            return $next($request);
        }

        // Authenticate from ?token= query parameter (mobile app downloads)
        $token = $request->query('token');
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
                if ($user && $user->status === 'active') {
                    // Set the user on the default web guard
                    auth('web')->login($user);
                    return $next($request);
                }
            }
        }

        // Not authenticated — abort
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return $next($request);
    }
}

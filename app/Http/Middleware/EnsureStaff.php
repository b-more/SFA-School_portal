<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Staff-only guard for routes a parent should never reach even if their token
 * is valid (e.g. bulk teacher endpoints, all-submissions downloads).
 * Returns 401/403 JSON rather than redirecting to a nonexistent login route.
 */
class EnsureStaff
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->isParent()) {
            return response()->json(['message' => 'Forbidden — staff only.'], 403);
        }

        return $next($request);
    }
}

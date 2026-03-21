<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            $changePasswordUrl = url('/admin/force-password-change');

            if ($request->url() !== $changePasswordUrl && !$request->is('admin/logout', 'livewire/*')) {
                return redirect($changePasswordUrl);
            }
        }

        return $next($request);
    }
}

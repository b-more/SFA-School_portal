<?php

namespace App\Http\Middleware;

use App\Constants\RoleConstants;
use App\Filament\Pages\DriverDashboard;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * For users with role_id = DRIVER, block every admin Resource route. Filament's
 * Resource::canAccess() defaults to true with no Policy, so nothing else stops a
 * driver from typing /admin/students into the URL bar — this middleware does.
 *
 * Pages and auth/profile routes pass through unchanged: those have their own
 * canAccess() role gates which 403 correctly for drivers, and the panel root
 * (filament.admin.pages.dashboard) is handled by Dashboard::mount() which
 * redirects drivers to DriverDashboard.
 */
class RestrictDriverRoutes
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role_id !== RoleConstants::DRIVER) {
            return $next($request);
        }

        $name = (string) ($request->route()?->getName() ?? '');

        if (str_starts_with($name, 'filament.admin.resources.')) {
            return redirect()->to(DriverDashboard::getUrl());
        }

        return $next($request);
    }
}

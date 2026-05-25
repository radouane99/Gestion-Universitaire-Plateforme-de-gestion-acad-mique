<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ProtectSensitiveRoutes
{
    /**
     * Handle an incoming request.
     *
     * Abort with 403 if the application is not in local environment
     * and the authenticated user is not an admin.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!app()->environment('local')) {
            $user = $request->user();
            if (!$user || !$user->isAdmin()) {
                abort(403, 'This route is restricted to administrators in non‑local environments.');
            }
        }
        return $next($request);
    }
}

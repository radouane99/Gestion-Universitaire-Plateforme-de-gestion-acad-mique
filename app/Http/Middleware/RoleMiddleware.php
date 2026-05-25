<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    /**
     * Handle an incoming request.
     *
     * @param  \Closure  $next
     * @param  string    $role   Expected role name (admin, professor, student)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        // No authenticated user → deny access
        if (!$user) {
            abort(403, 'Access denied. Not authenticated.');
        }

        // Ensure the role relationship is loaded to avoid null errors
        $user->loadMissing('role');

        // Compare role names case‑insensitively
        $hasRole = $user->role && strcasecmp($user->role->name, $role) === 0;

        if (!$hasRole) {
            abort(403, "Access denied. {$role}s only.");
        }

        return $next($request);
    }
}

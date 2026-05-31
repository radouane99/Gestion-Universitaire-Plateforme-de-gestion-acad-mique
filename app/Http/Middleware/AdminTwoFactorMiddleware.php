<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Apply 2FA restriction solely to Admins who have activated it
            if ($user->isAdmin() && $user->google2fa_enabled) {
                
                // Allow them to access the 2FA verification challenge routes, logout, or basic locale switches without loop redirects
                if ($request->is('login/2fa') || $request->routeIs('admin.2fa.*') || $request->is('logout') || $request->is('lang/*')) {
                    return $next($request);
                }

                // Redirect to 2FA challenge if not verified in the current session
                if (!session()->get('admin_2fa_verified')) {
                    return redirect()->route('admin.2fa.challenge');
                }
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;
        
        // Convert string role to enum if needed
        if (is_string($userRole)) {
            $userRole = UserRole::tryFrom($userRole);
        }

        // Check if user has one of the allowed roles
        foreach ($roles as $role) {
            $allowedRole = UserRole::tryFrom($role);
            if ($allowedRole && $userRole === $allowedRole) {
                return $next($request);
            }
        }

        // Also allow admin and manager to access everything
        if ($userRole === UserRole::Admin || $userRole === UserRole::Manager) {
            return $next($request);
        }

        abort(403, 'Unauthorized. You do not have the required role to access this page.');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  Can be a single role or comma-separated list of roles
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Support multiple roles separated by comma
        $roles = array_map('trim', explode(',', $role));

        // Check if user has at least one of the required roles
        $hasRequiredRole = false;
        foreach ($roles as $requiredRole) {
            if ($user->hasRole($requiredRole)) {
                $hasRequiredRole = true;
                break;
            }
        }

        if (!$hasRequiredRole) {
            abort(403, 'Access denied. You do not have the required role.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        // If not authenticated, redirect to login
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Allow admins to access everything
        if (isset($user->role) && $user->role === 'admin') {
            return $next($request);
        }

        // If user's role matches one of the allowed roles, continue
        if (! isset($user->role) || ! in_array($user->role, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}

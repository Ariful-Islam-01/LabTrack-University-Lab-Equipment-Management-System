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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Read the user's role from the session
        $userRole = session('role');

        // If the user is not logged in (session has no role), redirect to /login
        if (!$userRole) {
            return redirect('/login');
        }

        // If the user's role is NOT inside the allowed roles, redirect to dashboard with an "Access Denied" flash message
        if (!in_array($userRole, $roles)) {
            return redirect('/dashboard')->with('error', 'Access Denied');
        }

        // If the role matches any allowed role, continue the request
        return $next($request);
    }
}

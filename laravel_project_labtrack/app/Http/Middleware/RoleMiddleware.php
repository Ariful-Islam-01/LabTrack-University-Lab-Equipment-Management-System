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
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Read the user's role from the session
        $userRole = $request->session()->get('role');

        // If the user is not logged in (session has no role), redirect to /login
        if (!$userRole) {
            return redirect('/login');
        }

        // If the role does not match, redirect to /dashboard with an "Access Denied" flash message
        if ($userRole !== $role) {
            return redirect('/dashboard')->with('error', 'Access Denied');
        }

        // If the role matches, allow the request
        return $next($request);
    }
}

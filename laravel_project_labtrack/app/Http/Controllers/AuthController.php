<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        // Validate email and password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find user where email and password match using Query Builder
        $user = DB::table('users')
            ->where('email', $request->email)
            ->where('password', $request->password)
            ->first();

        // If no user exists, redirect back with errors and preserve email input
        if (!$user) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->withInput($request->only('email'));
        }

        // Store ONLY specified session variables
        $request->session()->put('user_id', $user->user_id);
        $request->session()->put('full_name', $user->full_name);
        $request->session()->put('role', $user->role);

        // Regenerate the session
        $request->session()->regenerate();

        // Redirect to dashboard
        return redirect()->route('dashboard');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Redirect to login
        return redirect()->route('login');
    }
}

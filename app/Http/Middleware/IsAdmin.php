<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user has admin or manager role
        if ($user->role && in_array($user->role->name, ['admin', 'manager'])) {
            return $next($request);
        }

        // If not admin/manager, redirect to staff dashboard
        abort(403, 'Unauthorized access. Admin or Manager role required.');
    }
}

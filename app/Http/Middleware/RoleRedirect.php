<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->role) {
                switch ($user->role->name) {
                    case 'admin':
                    case 'manager':
                        return redirect()->route('admin.dashboard');
                    case 'staff':
                        return redirect()->route('staff.dashboard');
                }
            }
        }

        return $next($request);
    }
}

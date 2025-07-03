<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        $path = $request->path();
        
        // If specific roles are required
        if (!empty($roles) && !in_array($user->role, $roles)) {
            return $this->redirectBasedOnRole($user->role);
        }
        
        // Specific path restrictions regardless of route middleware
        
        // Admin website paths - only admin_website can access
        if (str_starts_with($path, 'admin/') || $path === 'admin') {
            if ($user->role !== 'admin_website') {
                return $this->redirectBasedOnRole($user->role);
            }
        }
        
        // Admin grup paths - only admin_grup can access
        if (str_starts_with($path, 'grup/') || $path === 'grup') {
            if ($user->role !== 'admin_grup') {
                return $this->redirectBasedOnRole($user->role);
            }
        }

        // UKM paths are accessible by all authenticated users
        // UKM index, join, leave, chat should be accessible by all roles
        if (str_starts_with($path, 'ukm/') || $path === 'ukm') {
            // Allow all authenticated users to access UKM features
            return $next($request);
        }
        
        // Chat paths are accessible by all authenticated users
        // Chat functionality should not be restricted by role
        if (str_starts_with($path, 'chat/') || $path === 'chat') {
            // Allow all authenticated users to access chat features
            return $next($request);
        }

        // Return the appropriate view for profile and other sections
        // Controller logic will handle which specific view to use
        return $next($request);
    }
    
    /**
     * Redirect user to appropriate page based on role
     *
     * @param string $role
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectBasedOnRole($role)
    {
        switch ($role) {
            case 'admin_website':
                return redirect('/admin/dashboard');
            case 'admin_grup':
                return redirect('/grup/dashboard');
            default:
                return redirect()->route('ukm.index');
        }
    }
}

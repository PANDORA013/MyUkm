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
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Jika user mengakses halaman yang bukan untuk role-nya, redirect ke halaman yang sesuai
            $path = $request->path();
            
            // Jika user bukan admin_website tetapi mengakses halaman admin
            if ($user->role !== 'admin_website' && str_starts_with($path, 'admin/')) {
                if ($user->role === 'admin_grup') {
                    return redirect('/grup/dashboard');
                }
                return redirect()->route('ukm.index');
            }
            
            // Jika user bukan admin_grup tetapi mengakses halaman grup admin
            if ($user->role !== 'admin_grup' && str_starts_with($path, 'grup/')) {
                if ($user->role === 'admin_website') {
                    return redirect('/admin/dashboard');
                }
                return redirect()->route('ukm.index');
            }
        }
        
        return $next($request);
    }
}

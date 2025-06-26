<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AdminWebsiteMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin_website') {
            return redirect()->route('login')
                ->with('error', 'Unauthorized access. Please login with admin credentials.');
        }
        
        return $next($request);
    }
}

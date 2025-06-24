<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminWebsiteMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Add your middleware logic here
        // For example, check if user has admin_website role
        
        return $next($request);
    }
}

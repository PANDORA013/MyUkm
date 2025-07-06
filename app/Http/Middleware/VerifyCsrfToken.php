<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     */
    protected $except = [
        'broadcasting/auth',
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {        
        // Skip CSRF verification completely in testing environment
        if (app()->environment('testing')) {
            return $next($request);
        }
        
        // For debugging in tests
        if (config('app.env') === 'testing') {
            return $next($request);
        }
        
        // Skip CSRF verification in local environment when debug is enabled
        if (app()->environment('local') && config('app.debug')) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}

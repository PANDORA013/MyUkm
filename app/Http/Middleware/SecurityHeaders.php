<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Content Security Policy
        $response->headers->set('Content-Security-Policy', 
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' js.pusher.com; " .
            "style-src 'self' 'unsafe-inline' fonts.googleapis.com; " .
            "font-src 'self' fonts.gstatic.com; " .
            "img-src 'self' data: blob:; " .
            "connect-src 'self' ws: wss: soketi.app pusher.com; " .
            "frame-ancestors 'none';"
        );

        // Improved Cache-Control header (remove problematic directives)
        if ($request->is('api/*') || $request->ajax()) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0');
        } else {
            $response->headers->set('Cache-Control', 'public, max-age=3600');
        }

        // Remove Expires header in favor of Cache-Control
        $response->headers->remove('Expires');

        // Ensure cookies are secure in production
        if (app()->environment('production')) {
            $response->headers->set('Set-Cookie', 
                $response->headers->get('Set-Cookie') . '; Secure; SameSite=Strict'
            );
        }

        // Set proper charset for content-type
        if ($response->headers->get('Content-Type')) {
            $contentType = $response->headers->get('Content-Type');
            if (strpos($contentType, 'text/') === 0 && strpos($contentType, 'charset=') === false) {
                $response->headers->set('Content-Type', $contentType . '; charset=utf-8');
            }
        }

        return $response;
    }
}

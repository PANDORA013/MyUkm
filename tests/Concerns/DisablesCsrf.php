<?php

namespace Tests\Concerns;

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

/**
 * Trait to completely disable CSRF protection during testing
 */
trait DisablesCsrf
{
    /**
     * Setup CSRF disabling for tests
     */
    protected function setUpDisablesCsrf(): void
    {
        // Multiple approaches to ensure CSRF is disabled
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ]);
        
        // Force environment to testing
        $this->app['env'] = 'testing';
        config(['app.env' => 'testing']);
        
        // Override CSRF verification method
        $this->app->bind(
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            function () {
                return new class extends \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken {
                    protected function shouldPassThrough($request): bool
                    {
                        return true; // Always pass through in testing
                    }
                };
            }
        );
        
        // Also override our custom CSRF middleware
        $this->app->bind(
            \App\Http\Middleware\VerifyCsrfToken::class,
            function () {
                return new class extends \App\Http\Middleware\VerifyCsrfToken {
                    public function handle($request, \Closure $next)
                    {
                        return $next($request); // Always skip CSRF in testing
                    }
                };
            }
        );
    }
    
    /**
     * Make a POST request without CSRF concerns
     */
    protected function postWithoutCsrf(string $uri, array $data = [], array $headers = [])
    {
        return $this->post($uri, $data, $headers);
    }
    
    /**
     * Make an authenticated POST request without CSRF concerns
     */
    protected function authenticatedPost($user, string $uri, array $data = [], array $headers = [])
    {
        return $this->actingAs($user)->post($uri, $data, $headers);
    }
}

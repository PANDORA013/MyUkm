<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Temporary workaround for fileinfo issue in Laravel Ignition
        // Disable file context collection in Flare/Ignition to avoid MIME type detection
        if (config('app.debug') && class_exists(\Spatie\LaravelIgnition\FlareMiddleware\AddContext::class)) {
            app()->bind(\Spatie\FlareClient\Context\RequestContextProvider::class, function () {
                return new class {
                    public function toArray() { 
                        return [
                            'method' => request()->method(),
                            'url' => request()->url(),
                            'headers' => [],
                            'body' => [],
                            'files' => [] // Skip files to avoid MIME detection
                        ]; 
                    }
                };
            });
        }
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('Illuminate\Contracts\Broadcasting\Factory', function ($app) {
            return new \Illuminate\Broadcasting\BroadcastManager($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure broadcasting is enabled
        if (config('broadcasting.default') !== 'null') {
            // Set up broadcasting routes with proper middleware
            Broadcast::routes([
                'middleware' => ['web', 'auth']
            ]);
        }

        require base_path('routes/channels.php');
    }
}
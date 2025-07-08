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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Only set up broadcasting if it's enabled
        $broadcastDriver = config('broadcasting.default');
        
        if ($broadcastDriver && $broadcastDriver !== 'null') {
            // Set up broadcasting routes with proper middleware
            Broadcast::routes([
                'middleware' => ['web', 'auth']
            ]);
        }

        require base_path('routes/channels.php');
    }
}
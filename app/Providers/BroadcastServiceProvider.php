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
     */    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['web', 'auth']]);

        require base_path('routes/channels.php');
    }
}
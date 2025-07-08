<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define the 'isAdminWebsite' gate
        Gate::define('isAdminWebsite', function (User $user) {
            return $user->role === 'admin_website';
        });

        // Define the 'isAdminGrup' gate
        Gate::define('isAdminGrup', function (User $user) {
            return $user->role === 'admin_grup';
        });

        // Define the 'isMember' gate
        Gate::define('isMember', function (User $user) {
            return $user->role === 'member';
        });
    }
}

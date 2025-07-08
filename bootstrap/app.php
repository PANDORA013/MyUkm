<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global and route middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'last_seen' => \App\Http\Middleware\UpdateLastSeen::class,
            'admin_website' => \App\Http\Middleware\AdminWebsiteMiddleware::class,
            'ensure.role' => \App\Http\Middleware\EnsureUserRole::class
        ]);
        // Apply last_seen middleware to all web routes
        $middleware->appendToGroup('web', 'last_seen');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

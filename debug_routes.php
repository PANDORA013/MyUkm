<?php

// Quick test script untuk debug 403 issue
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test route registration
$router = $app['router'];
$routes = $router->getRoutes();

echo "=== Testing Route Registration ===\n";
echo "Looking for UKM routes...\n\n";

foreach ($routes as $route) {
    $uri = $route->uri();
    if (str_contains($uri, 'ukm')) {
        echo "Method: " . implode('|', $route->methods()) . "\n";
        echo "URI: " . $uri . "\n";
        echo "Name: " . $route->getName() . "\n";
        echo "Middleware: " . implode(', ', $route->middleware()) . "\n";
        echo "---\n";
    }
}

echo "\n=== Testing Middleware Registration ===\n";
$middleware = $app['router']->getMiddleware();
foreach ($middleware as $name => $class) {
    echo "$name => $class\n";
}

echo "\n=== Testing Database Connection ===\n";
try {
    $db = $app['db'];
    $pdo = $db->connection()->getPdo();
    echo "Database connected successfully!\n";
    
    // Check if users table exists and has data
    $userCount = $db->table('users')->count();
    echo "Users in database: $userCount\n";
    
    if ($userCount > 0) {
        $users = $db->table('users')->select('id', 'name', 'role')->get();
        echo "Users:\n";
        foreach ($users as $user) {
            echo "- {$user->id}: {$user->name} ({$user->role})\n";
        }
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";

<?php

require_once 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->bind(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DATABASE CHECK ===" . PHP_EOL;
echo "Users: " . App\Models\User::count() . PHP_EOL;
echo "Groups: " . App\Models\Group::count() . PHP_EOL;
echo "UKMs: " . App\Models\UKM::count() . PHP_EOL;

// Check first user
$firstUser = App\Models\User::first();
if ($firstUser) {
    echo "First user: " . $firstUser->name . " (Role: " . $firstUser->role . ")" . PHP_EOL;
} else {
    echo "No users found!" . PHP_EOL;
}

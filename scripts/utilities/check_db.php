<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Laravel 11
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

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

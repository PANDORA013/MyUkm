<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Laravel 11
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\UserDeletionHistory;
use Illuminate\Support\Facades\DB;

echo "=== TEST BOOTSTRAP DELETE SCRIPT ===" . PHP_EOL;

// Test koneksi database dan model
$userCount = User::count();
echo "Total users: " . $userCount . PHP_EOL;

// Test model UserDeletionHistory
$historyCount = UserDeletionHistory::count();
echo "Total deletion history: " . $historyCount . PHP_EOL;

echo "Bootstrap script berfungsi dengan baik!" . PHP_EOL;

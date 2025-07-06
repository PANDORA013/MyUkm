<?php

require __DIR__.'/../../../vendor/autoload.php';
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Database Test ===\n\n";

try {
    // Test connection
    $results = DB::select('SELECT 1 as test');
    echo "Database connection successful!\n";
    print_r($results);
} catch (\Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}

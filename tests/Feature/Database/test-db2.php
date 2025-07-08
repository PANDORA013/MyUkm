<?php

require __DIR__.'/../../../vendor/autoload.php';
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Database Test 2 ===\n\n";

try {
    // Test connection
    $results = DB::select('SELECT 1 as test');
    echo "Database connection successful!\n";
    
    // List all tables
    $tables = DB::select('SHOW TABLES');
    echo "\nTables in database:\n";
    foreach ($tables as $table) {
        foreach ($table as $key => $value) {
            echo "- $value\n";
        }
    }
    
    // Show users table structure if it exists
    try {
        $columns = DB::select('DESCRIBE users');
        echo "\nUsers table structure:\n";
        foreach ($columns as $column) {
            echo "- {$column->Field} ({$column->Type})\n";
        }
    } catch (\Exception $e) {
        echo "\nCould not describe users table: " . $e->getMessage() . "\n";
    }
    
} catch (\Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}

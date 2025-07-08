<?php

require __DIR__.'/../../../vendor/autoload.php';
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Database Connection Test ===\n\n";

try {
    // Test connection
    DB::connection()->getPdo();
    echo "Connected successfully to the database!\n";
    
    // Test query
    $users = DB::table('users')->count();
    echo "Total users: " . $users . "\n";
    
    // Test groups table
    $groups = DB::table('groups')->count();
    echo "Total groups: " . $groups . "\n";
    
    // Show tables
    $tables = DB::select('SHOW TABLES');
    echo "\nTables in database:\n";
    foreach ($tables as $table) {
        foreach ($table as $key => $value) {
            echo "- " . $value . "\n";
        }
    }
    
} catch (\Exception $e) {
    die("Could not connect to the database. Error: " . $e->getMessage() . "\n");
}

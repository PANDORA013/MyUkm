<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

$config = [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'myukm_test',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
];

$db = new DB;
$db->addConnection($config);
$db->setAsGlobal();
$db->bootEloquent();

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

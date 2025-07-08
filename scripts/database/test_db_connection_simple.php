<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

try {
    // Test database connection
    echo "Testing database connection...\n";
    DB::connection()->getPdo();
    echo "✓ Connected to database\n";
    
    // Get database name
    $database = DB::selectOne('SELECT DATABASE() as db');
    echo "Current database: " . ($database->db ?? 'unknown') . "\n";
    
    // Get tables
    $tables = DB::select('SHOW TABLES');
    echo "\nTables in database:";
    if (empty($tables)) {
        echo " No tables found\n";
    } else {
        echo "\n";
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            echo "- $tableName\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if ($e->getPrevious()) {
        echo "Previous: " . $e->getPrevious()->getMessage() . "\n";
    }
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

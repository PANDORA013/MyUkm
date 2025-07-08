<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illware\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: text/plain');

try {
    // Test database connection
    echo "=== Testing Database Connection ===\n";
    DB::connection()->getPdo();
    echo "✓ Connected to database\n";
    
    // Get database name
    $database = DB::selectOne('SELECT DATABASE() as db');
    echo "Current database: " . ($database->db ?? 'unknown') . "\n\n";
    
    // Check if groups table exists
    echo "=== Checking Groups Table ===\n";
    if (!Schema::hasTable('groups')) {
        die("The 'groups' table does not exist.\n");
    }
    
    echo "✓ 'groups' table exists\n\n";
    
    // Get table columns
    echo "=== Table Columns ===\n";
    $columns = Schema::getColumnListing('groups');
    
    if (empty($columns)) {
        echo "No columns found in 'groups' table\n";
    } else {
        echo "Columns in 'groups' table (" . count($columns) . "):\n";
        foreach ($columns as $column) {
            $columnType = DB::getSchemaBuilder()->getColumnType('groups', $column);
            echo "- $column ($columnType)\n";
        }
    }
    
    // Check for referral_code column
    echo "\n=== Checking referral_code Column ===\n";
    if (!in_array('referral_code', $columns)) {
        echo "✗ 'referral_code' column does not exist\n";
    } else {
        $columnType = DB::getSchemaBuilder()->getColumnType('groups', 'referral_code');
        echo "- Column 'referral_code' exists with type: $columnType\n";
        
        // Check column details
        $columnDetails = DB::selectOne(
            "SELECT COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT, EXTRA 
             FROM information_schema.COLUMNS 
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'groups' AND COLUMN_NAME = 'referral_code'",
            [$database->db]
        );
        
        if ($columnDetails) {
            echo "  - Type: {$columnDetails->COLUMN_TYPE}\n";
            echo "  - Nullable: {$columnDetails->IS_NULLABLE}\n";
            echo "  - Key: {$columnDetails->COLUMN_KEY}\n";
            echo "  - Default: " . ($columnDetails->COLUMN_DEFAULT ?? 'NULL') . "\n";
            echo "  - Extra: {$columnDetails->EXTRA}\n";
        }
    }
    
    // Check indexes
    echo "\n=== Table Indexes ===\n";
    $indexes = DB::select("SHOW INDEX FROM `groups`");
    
    if (empty($indexes)) {
        echo "No indexes found on 'groups' table\n";
    } else {
        $indexGroups = [];
        
        foreach ($indexes as $index) {
            $name = $index->Key_name;
            if (!isset($indexGroups[$name])) {
                $indexGroups[$name] = [
                    'unique' => !$index->Non_unique,
                    'type' => $index->Index_type,
                    'columns' => []
                ];
            }
            $indexGroups[$name]['columns'][$index->Seq_in_index] = $index->Column_name;
        }
        
        foreach ($indexGroups as $name => $index) {
            echo "- $name\n";
            echo "  Type: {$index['type']}\n";
            echo "  Unique: " . ($index['unique'] ? 'Yes' : 'No') . "\n";
            echo "  Columns: " . implode(', ', $index['columns']) . "\n\n";
        }
    }
    
    // Check for any data
    $count = DB::table('groups')->count();
    echo "\nNumber of records in groups table: $count\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if ($e->getPrevious()) {
        echo "Previous: " . $e->getPrevious()->getMessage() . "\n";
    }
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

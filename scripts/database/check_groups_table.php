<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain');

try {
    // Check if groups table exists
    $tables = DB::select('SHOW TABLES LIKE "groups"');
    
    if (empty($tables)) {
        die("The 'groups' table does not exist.\n");
    }
    
    echo "=== Groups Table Structure ===\n";
    
    // Get table structure
    $columns = DB::select('SHOW COLUMNS FROM `groups`');
    
    echo "\nColumns in 'groups' table:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})";
        echo $column->Null === 'NO' ? ' NOT NULL' : '';
        echo $column->Key ? " [{$column->Key}]" : '';
        echo "\n";
    }
    
    // Check for indexes on referral_code
    $indexes = DB::select("SHOW INDEX FROM `groups` WHERE Column_name = 'referral_code'");
    
    if (empty($indexes)) {
        echo "\nNo indexes found on 'referral_code' column\n";
    } else {
        echo "\nIndexes on 'referral_code' column:\n";
        foreach ($indexes as $index) {
            echo "- {$index->Key_name} ({$index->Index_type})\n";
        }
    }
    
    // Check for foreign key constraints
    $fks = DB::select("
        SELECT 
            TABLE_NAME, COLUMN_NAME, 
            CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE 
            TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'groups'
            AND COLUMN_NAME = 'referral_code'
            AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    
    if (empty($fks)) {
        echo "\nNo foreign key constraints found on 'referral_code' column\n";
    } else {
        echo "\nForeign key constraints on 'referral_code' column:\n";
        foreach ($fks as $fk) {
            echo "- {$fk->CONSTRAINT_NAME}: {$fk->TABLE_NAME}.{$fk->COLUMN_NAME} ";
            echo "REFERENCES {$fk->REFERENCED_TABLE_NAME}({$fk->REFERENCED_COLUMN_NAME})\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if ($e->getPrevious()) {
        echo "Previous: " . $e->getPrevious()->getMessage() . "\n";
    }
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

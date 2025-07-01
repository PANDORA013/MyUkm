<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

$db = new DB;

$db->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'myukm_test',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$db->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$db->bootEloquent();

try {
    // Check groups table structure
    $columns = DB::select('SHOW COLUMNS FROM groups');
    echo "Groups table structure:\n";
    echo str_pad("Field", 20) . str_pad("Type", 30) . str_pad("Null", 10) . str_pad("Key", 10) . str_pad("Default", 20) . "Extra\n";
    echo str_repeat("-", 90) . "\n";
    
    foreach ($columns as $column) {
        echo str_pad($column->Field, 20) . 
             str_pad($column->Type, 30) . 
             str_pad($column->Null, 10) . 
             str_pad($column->Key, 10) . 
             str_pad($column->Default ?? 'NULL', 20) . 
             $column->Extra . "\n";
    }
    
    // Check indexes
    $indexes = DB::select("SHOW INDEX FROM groups");
    echo "\nIndexes on groups table:\n";
    foreach ($indexes as $index) {
        echo "- " . $index->Key_name . " (" . $index->Column_name . ")\n";
    }
    
    // Check data in groups table
    echo "\nData in groups table:\n";
    $groups = DB::table('groups')->get();
    foreach ($groups as $group) {
        echo "ID: " . $group->id . 
             ", Name: " . $group->name . 
             ", Referral Code: " . $group->referral_code . 
             ", Description: " . ($group->description ?? 'NULL') . "\n";
    }
    
} catch (\Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}

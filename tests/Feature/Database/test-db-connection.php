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

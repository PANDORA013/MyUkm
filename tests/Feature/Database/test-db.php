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
    $results = DB::select('SELECT 1 as test');
    echo "Database connection successful!\n";
    print_r($results);
} catch (\Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}

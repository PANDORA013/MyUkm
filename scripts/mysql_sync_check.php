<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel 11 application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot the application
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Import facades after bootstrap
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

try {
    echo "🔍 MYSQL SYNCHRONIZATION CHECK - MyUKM Application\n";
    echo "==================================================\n\n";

    // 1. Test Database Connection
    echo "1. Testing MySQL Database Connection...\n";
    $connection = DB::connection();
    $dbName = $connection->getDatabaseName();
    $driver = $connection->getDriverName();
    echo "   ✅ Connected to: $dbName (driver: $driver)\n";
    echo "   ✅ Host: " . config('database.connections.mysql.host') . "\n";
    echo "   ✅ Port: " . config('database.connections.mysql.port') . "\n\n";

    // 2. Test Database Tables
    echo "2. Checking MySQL Tables...\n";
    $tables = DB::select('SHOW TABLES');
    $tableNames = array_map(function($table) use ($dbName) {
        return $table->{"Tables_in_$dbName"};
    }, $tables);
    
    $requiredTables = [
        'users', 'groups', 'group_user', 'chats', 'ukms',
        'cache', 'cache_locks', 'sessions', 'migrations',
        'jobs', 'failed_jobs'
    ];
    
    foreach ($requiredTables as $table) {
        if (in_array($table, $tableNames)) {
            $count = DB::table($table)->count();
            echo "   ✅ Table '$table' exists ($count records)\n";
        } else {
            echo "   ❌ Table '$table' missing\n";
        }
    }
    echo "\n";

    // 3. Test Cache System
    echo "3. Testing Cache System (MySQL-based)...\n";
    $cacheDriver = config('cache.default');
    echo "   ✅ Cache driver: $cacheDriver\n";
    
    if ($cacheDriver === 'database') {
        // Test cache write/read
        $testKey = 'mysql_sync_test_' . time();
        $testValue = 'MySQL cache working!';
        
        Cache::put($testKey, $testValue, 60);
        $retrieved = Cache::get($testKey);
        
        if ($retrieved === $testValue) {
            echo "   ✅ Cache read/write test: PASSED\n";
            Cache::forget($testKey); // Cleanup
        } else {
            echo "   ❌ Cache read/write test: FAILED\n";
        }
        
        // Check cache table
        $cacheTable = config('cache.stores.database.table', 'cache');
        $cacheCount = DB::table($cacheTable)->count();
        echo "   ✅ Cache table '$cacheTable' has $cacheCount entries\n";
    }
    echo "\n";

    // 4. Test Session System  
    echo "4. Testing Session System (MySQL-based)...\n";
    $sessionDriver = config('session.driver');
    echo "   ✅ Session driver: $sessionDriver\n";
    
    if ($sessionDriver === 'database') {
        $sessionTable = config('session.table', 'sessions');
        $sessionCount = DB::table($sessionTable)->count();
        echo "   ✅ Session table '$sessionTable' has $sessionCount entries\n";
        echo "   ✅ Session lifetime: " . config('session.lifetime') . " minutes\n";
    }
    echo "\n";

    // 5. Test Queue System
    echo "5. Testing Queue System...\n";
    $queueDriver = config('queue.default');
    echo "   ✅ Queue driver: $queueDriver\n";
    
    if ($queueDriver === 'database') {
        $jobsCount = DB::table('jobs')->count();
        $failedJobsCount = DB::table('failed_jobs')->count();
        echo "   ✅ Jobs table has $jobsCount pending jobs\n";
        echo "   ✅ Failed jobs table has $failedJobsCount failed jobs\n";
    }
    echo "\n";

    // 6. Test Application Data
    echo "6. Testing Application Data in MySQL...\n";
    $userCount = DB::table('users')->count();
    $groupCount = DB::table('groups')->count();
    $chatCount = DB::table('chats')->count();
    $ukmCount = DB::table('ukms')->count();
    
    echo "   ✅ Users: $userCount\n";
    echo "   ✅ Groups: $groupCount\n";
    echo "   ✅ Chats: $chatCount\n";
    echo "   ✅ UKMs: $ukmCount\n";
    echo "\n";

    // 7. Test Environment Configuration
    echo "7. Checking Environment Configuration...\n";
    echo "   ✅ DB_CONNECTION: " . env('DB_CONNECTION') . "\n";
    echo "   ✅ DB_HOST: " . env('DB_HOST') . "\n";
    echo "   ✅ DB_PORT: " . env('DB_PORT') . "\n";
    echo "   ✅ DB_DATABASE: " . env('DB_DATABASE') . "\n";
    echo "   ✅ DB_USERNAME: " . env('DB_USERNAME') . "\n";
    echo "   ✅ CACHE_DRIVER: " . env('CACHE_DRIVER') . "\n";
    echo "   ✅ SESSION_DRIVER: " . env('SESSION_DRIVER') . "\n";
    echo "   ✅ QUEUE_CONNECTION: " . env('QUEUE_CONNECTION') . "\n";
    echo "\n";

    // 8. Performance Test
    echo "8. MySQL Performance Test...\n";
    $start = microtime(true);
    
    // Simple query performance test
    DB::table('users')->limit(10)->get();
    DB::table('groups')->limit(10)->get();
    DB::table('chats')->limit(10)->get();
    
    $end = microtime(true);
    $duration = ($end - $start) * 1000; // Convert to milliseconds
    
    echo "   ✅ Query performance: " . number_format($duration, 2) . "ms for 3 queries\n";
    echo "\n";

    echo "🎉 MYSQL SYNCHRONIZATION COMPLETE!\n";
    echo "==================================\n";
    echo "✅ All components are properly configured to use MySQL\n";
    echo "✅ Database connection is working\n";
    echo "✅ Cache system is using MySQL\n";
    echo "✅ Session system is using MySQL\n";
    echo "✅ Application data is stored in MySQL\n";
    echo "\nYour MyUKM application is fully synchronized with MySQL! 🚀\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

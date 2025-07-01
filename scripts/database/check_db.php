<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: text/plain');

try {
    // Test database connection
    echo "Testing database connection...\n";
    DB::connection()->getPdo();
    echo "✓ Connected to database\n\n";
    
    // Check if groups table exists
    if (!Schema::hasTable('groups')) {
        die("❌ The 'groups' table does not exist.\n");
    }
    
    echo "✓ 'groups' table exists\n";
    
    // Check if referral_code column exists
    if (!Schema::hasColumn('groups', 'referral_code')) {
        die("❌ 'referral_code' column does not exist in 'groups' table.\n");
    }
    
    echo "✓ 'referral_code' column exists\n";
    
    // Get column details
    $column = DB::selectOne("SHOW COLUMNS FROM `groups` WHERE Field = 'referral_code'");
    echo "\nColumn details for 'referral_code':\n";
    print_r($column);
    
    // Check for unique constraint
    $constraints = DB::select("SHOW INDEX FROM `groups` WHERE Column_name = 'referral_code'");
    
    if (empty($constraints)) {
        echo "\n⚠ No unique constraint found on 'referral_code' column\n";
    } else {
        echo "\nConstraints on 'referral_code' column:\n";
        print_r($constraints);
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    if ($e->getPrevious()) {
        echo "Previous: " . $e->getPrevious()->getMessage() . "\n";
    }
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

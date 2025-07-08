<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illware\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: text/plain');

echo "=== Groups Table Structure ===\n";

// Check if table exists
if (!Schema::hasTable('groups')) {
    die("The 'groups' table does not exist.\n");
}

// Get table columns
$columns = Schema::getColumnListing('groups');
echo "\nColumns in 'groups' table:\n";
print_r($columns);

// Get column details for referral_code
$referralCodeColumn = DB::selectOne(
    "SELECT COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, CHARACTER_MAXIMUM_LENGTH 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = ? 
    AND TABLE_NAME = 'groups' 
    AND COLUMN_NAME = 'referral_code'",
    [config('database.connections.mysql.database')]
);

echo "\nReferral code column details:\n";
print_r($referralCodeColumn);

// Get constraints
$constraints = DB::select(
    "SELECT CONSTRAINT_NAME, CONSTRAINT_TYPE, TABLE_NAME, COLUMN_NAME 
    FROM information_schema.TABLE_CONSTRAINTS tc
    JOIN information_schema.KEY_COLUMN_USAGE kcu
        ON tc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
    WHERE tc.TABLE_SCHEMA = ? 
    AND tc.TABLE_NAME = 'groups'
    AND kcu.COLUMN_NAME = 'referral_code'",
    [config('database.connections.mysql.database')]
);

echo "\nConstraints on referral_code column:\n";
print_r($constraints);

// Get indexes
$indexes = DB::select(
    "SHOW INDEX FROM groups WHERE Column_name = 'referral_code'",
    [config('database.connections.mysql.database')]
);

echo "\nIndexes on referral_code column:\n";
print_r($indexes);

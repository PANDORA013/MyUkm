<?php

require __DIR__ . '/vendor/autoload.php';
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DATA VERIFICATION ===\n\n";

// Check admin user
$admin = DB::table('users')->first();
echo "ADMIN USER: {$admin->name} ({$admin->email})\n";
echo "Password hint: Try 'password', 'admin123', or '12345678'\n\n";

// Check groups
$groups = DB::table('groups')->get();
echo "AVAILABLE GROUPS:\n";
foreach ($groups as $group) {
    $code = $group->referral_code ?? sprintf('%04d', $group->id);
    echo "- {$group->name} [Code: {$code}]\n";
}

echo "\n=== TESTING CREDENTIALS ===\n";
echo "Login URL: http://localhost:8000/login\n";
echo "Admin Email: {$admin->email}\n";
echo "Admin Password: Try common passwords or check UserSeeder\n\n";

echo "Ready for manual testing! 🚀\n";

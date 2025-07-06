<?php

require __DIR__ . '/vendor/autoload.php';
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CREATING TEST USERS ===\n\n";

// Create test users
$testUsers = [
    ['name' => 'Test User 1', 'nim' => '1001', 'email' => 'user1@test.com', 'password' => 'password'],
    ['name' => 'Test User 2', 'nim' => '1002', 'email' => 'user2@test.com', 'password' => 'password'],
    ['name' => 'Test User 3', 'nim' => '1003', 'email' => 'user3@test.com', 'password' => 'password']
];

foreach ($testUsers as $userData) {
    // Check if user already exists
    $existing = DB::table('users')->where('email', $userData['email'])->first();
    if (!$existing) {
        DB::table('users')->insert([
            'name' => $userData['name'],
            'nim' => $userData['nim'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password']),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "✅ Created: {$userData['name']} (NIM: {$userData['nim']}, Email: {$userData['email']})\n";
    } else {
        echo "⚠️  Already exists: {$userData['name']} ({$userData['email']})\n";
    }
}

echo "\n=== CURRENT USERS FOR TESTING ===\n";
$users = DB::table('users')->get(['name', 'nim', 'email']);
foreach ($users as $user) {
    echo "- {$user->name} (NIM: {$user->nim}, Email: {$user->email}) [Password: password]\n";
}

echo "\n✅ Test users ready for multi-user testing!\n";

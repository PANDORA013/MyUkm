<?php

require __DIR__ . '/vendor/autoload.php';
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== MyUKM Test Data Check ===\n\n";

// Check users
$users = DB::table('users')->get(['id', 'name', 'email']);
echo "Users (" . $users->count() . " total):\n";
foreach ($users as $user) {
    echo "- {$user->name} ({$user->email}) [ID: {$user->id}]\n";
}

echo "\n";

// Check groups  
$groups = DB::table('groups')->get(['id', 'name', 'join_code']);
echo "Groups (" . $groups->count() . " total):\n";
foreach ($groups as $group) {
    echo "- {$group->name} [Code: {$group->join_code}] [ID: {$group->id}]\n";
}

echo "\n";

// Check existing chats
$chats = DB::table('chats')->count();
echo "Existing chat messages: {$chats}\n\n";

echo "=== MANUAL TESTING CREDENTIALS ===\n\n";

// Show test credentials
$adminUser = DB::table('users')->where('email', 'like', '%admin%')->first();
if ($adminUser) {
    echo "ADMIN LOGIN:\n";
    echo "Email: {$adminUser->email}\n";
    echo "Password: Check the UserSeeder or use 'password' as default\n\n";
}

$regularUser = DB::table('users')->where('email', 'not like', '%admin%')->first();
if ($regularUser) {
    echo "REGULAR USER LOGIN:\n";
    echo "Email: {$regularUser->email}\n";
    echo "Password: Check the UserSeeder or use 'password' as default\n\n";
}

if ($groups->count() > 0) {
    $firstGroup = $groups->first();
    echo "TEST GROUP TO JOIN:\n";
    echo "Group: {$firstGroup->name}\n";
    echo "Join Code: {$firstGroup->join_code}\n\n";
}

echo "=== REAL-TIME TESTING STEPS ===\n\n";
echo "1. Open multiple browser tabs to: http://localhost:8000\n";
echo "2. Login with the credentials above in different tabs\n";
echo "3. Join the same group using the join code\n";
echo "4. Start chatting and verify messages appear instantly in all tabs\n";
echo "5. Check the Queue Worker window for job processing\n\n";

echo "✓ Test data ready!\n";
echo "✓ Laravel server should be running on http://localhost:8000\n";
echo "✓ Queue worker should be processing jobs\n\n";
echo "Happy testing! 🚀\n";

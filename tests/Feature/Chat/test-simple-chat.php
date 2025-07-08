<?php

require __DIR__.'/../../../vendor/autoload.php';
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Simple Chat Test ===\n\n";

// Start transaction
DB::beginTransaction();

try {
    // Create a test user
    $userId = DB::table('users')->insertGetId([
        'name' => 'Test User',
        'nim' => '12345678',
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'role' => 'member',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Created user with ID: $userId\n";
    
    // Create a test group
    $groupId = DB::table('groups')->insertGetId([
        'name' => 'Test Group',
        'referral_code' => 'TST1',
        'description' => 'Test Description',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Created group with ID: $groupId and referral code: TST1\n";
    
    // Add user to group
    DB::table('group_user')->insert([
        'user_id' => $userId,
        'group_id' => $groupId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Added user to group\n";
    
    // Create a chat message
    $chatId = DB::table('chats')->insertGetId([
        'user_id' => $userId,
        'group_id' => $groupId,
        'message' => 'Hello World',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Created chat message with ID: $chatId\n";
    
    // Verify the chat was created
    $chat = DB::table('chats')->find($chatId);
    
    if ($chat) {
        echo "Chat verification successful!\n";
        echo "Message: " . $chat->message . "\n";
        echo "User ID: " . $chat->user_id . "\n";
        echo "Group ID: " . $chat->group_id . "\n";
    } else {
        echo "Error: Chat not found!\n";
    }
    
    // Rollback to clean up
    DB::rollBack();
    
    echo "Test completed successfully! (Changes rolled back)\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}

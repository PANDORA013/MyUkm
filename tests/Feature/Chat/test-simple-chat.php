<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Setup database connection
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

// Make this Capsule instance available globally via static methods...
$db->setAsGlobal();

// Setup the Eloquent ORM...
$db->bootEloquent();

// Start transaction
DB::beginTransaction();

try {
    // Create a test user
    $userId = DB::table('users')->insertGetId([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'role' => 'member',
    ]);
    
    echo "Created user with ID: $userId\n";
    
    // Create a test group
    $groupId = DB::table('groups')->insertGetId([
        'name' => 'Test Group',
        'referral_code' => 'TEST123',
        'description' => 'Test Description',
    ]);
    
    echo "Created group with ID: $groupId and referral code: TEST123\n";
    
    // Add user to group
    DB::table('group_user')->insert([
        'user_id' => $userId,
        'group_id' => $groupId,
    ]);
    
    echo "Added user to group\n";
    
    // Create a chat message
    $chatId = DB::table('chats')->insertGetId([
        'user_id' => $userId,
        'group_id' => $groupId,
        'message' => 'Hello World',
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

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
    echo "=== Starting Direct Chat Test ===\n\n";
    
    // 1. Create a test user
    $userId = DB::table('users')->insertGetId([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => DB::raw("PASSWORD('password')"),
        'role' => 'member',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    
    echo "✅ Created user with ID: $userId\n";
    
    // 2. Create a test group
    $groupId = DB::table('groups')->insertGetId([
        'name' => 'Test Group',
        'referral_code' => 'TEST123',
        'description' => 'Test Description',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    
    echo "✅ Created group with ID: $groupId and referral code: TEST123\n";
    
    // 3. Add user to group
    DB::table('group_user')->insert([
        'user_id' => $userId,
        'group_id' => $groupId,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    
    echo "✅ Added user to group\n";
    
    // 4. Create a chat message
    $chatId = DB::table('chats')->insertGetId([
        'user_id' => $userId,
        'group_id' => $groupId,
        'message' => 'Hello from direct test',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    
    echo "✅ Created chat message with ID: $chatId\n";
    
    // 5. Verify the chat was created
    $chat = DB::table('chats')->where('id', $chatId)->first();
    
    if ($chat) {
        echo "\n✅ Chat verification successful!\n";
        echo "Message: " . $chat->message . "\n";
        echo "User ID: " . $chat->user_id . "\n";
        echo "Group ID: " . $chat->group_id . "\n";
        
        // 6. Test the relationships
        $userChats = DB::table('chats')->where('user_id', $userId)->count();
        echo "\nUser has $userChats chat messages\n";
        
        $groupChats = DB::table('chats')->where('group_id', $groupId)->count();
        echo "Group has $groupChats chat messages\n";
    } else {
        echo "\n❌ Error: Chat not found!\n";
    }
    
    // 7. Test creating another chat message
    $message = 'Another test message';
    $newChatId = DB::table('chats')->insertGetId([
        'user_id' => $userId,
        'group_id' => $groupId,
        'message' => $message,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    
    if ($newChatId) {
        echo "\n✅ Created another chat message with ID: $newChatId\n";
        
        // Verify the new chat was created
        $verifyChat = DB::table('chats')->where('id', $newChatId)->first();
        if ($verifyChat) {
            echo "✅ Verified new chat in database\n";
            echo "Message: " . $verifyChat->message . "\n";
        } else {
            echo "❌ Error: Could not find new chat in database\n";
        }
    } else {
        echo "\n❌ Error: Failed to create another chat message\n";
    }
    
    // Rollback to clean up
    DB::rollBack();
    
    echo "\n=== Test completed successfully! (Changes rolled back) ===\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (" . $e->getLine() . ")\n";
    echo "Trace: \n" . $e->getTraceAsString() . "\n";
}

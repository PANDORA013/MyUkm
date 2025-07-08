<?php

require __DIR__.'/vendor/autoload.php';

use App\Models\User;
use App\Models\Group;
use App\Models\Chat;
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
    echo "=== Starting Core Chat Test ===\n\n";
    
    // 1. Create a test user
    $user = new User();
    $user->name = 'Test User';
    $user->email = 'test@example.com';
    // Use database's PASSWORD function directly since we're not using Laravel's hashing
    $user->password = DB::raw("PASSWORD('password')");
    $user->role = 'member';
    $user->save();
    
    echo "✅ Created user with ID: {$user->id}\n";
    
    // 2. Create a test group
    $group = new Group();
    $group->name = 'Test Group';
    $group->referral_code = 'TEST123';
    $group->description = 'Test Description';
    $group->save();
    
    echo "✅ Created group with ID: {$group->id} and referral code: {$group->referral_code}\n";
    
    // 3. Add user to group
    $user->groups()->attach($group->id);
    echo "✅ Added user to group\n\n";
    
    // 4. Create a chat message
    $chat = new Chat();
    $chat->user_id = $user->id;
    $chat->group_id = $group->id;
    $chat->message = 'Hello from core test';
    $chat->save();
    
    echo "✅ Created chat message with ID: {$chat->id}\n";
    
    // 5. Verify the chat was created
    $savedChat = Chat::find($chat->id);
    
    if ($savedChat) {
        echo "\n✅ Chat verification successful!\n";
        echo "Message: " . $savedChat->message . "\n";
        echo "User ID: " . $savedChat->user_id . "\n";
        echo "Group ID: " . $savedChat->group_id . "\n";
        
        // 6. Test the relationship
        $userChats = $user->chats()->get();
        echo "\nUser has " . $userChats->count() . " chat messages\n";
        
        $groupChats = $group->chats()->get();
        echo "Group has " . $groupChats->count() . " chat messages\n";
    } else {
        echo "\n❌ Error: Chat not found!\n";
    }
    
    // 7. Test the chat endpoint functionality directly
    echo "\n=== Testing Chat Endpoint Functionality ===\n";
    
    $message = 'Hello from endpoint test';
    $newChat = Chat::create([
        'user_id' => $user->id,
        'group_id' => $group->id,
        'message' => $message,
    ]);
    
    if ($newChat) {
        echo "✅ Created new chat via direct model: ID {$newChat->id}\n";
        
        // Verify the chat was created
        $verifyChat = Chat::where('message', $message)->first();
        if ($verifyChat) {
            echo "✅ Verified chat in database\n";
        } else {
            echo "❌ Error: Could not find chat in database\n";
        }
    } else {
        echo "❌ Error: Failed to create chat via direct model\n";
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

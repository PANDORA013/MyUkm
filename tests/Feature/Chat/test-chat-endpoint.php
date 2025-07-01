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
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Created user with ID: $userId\n";
    
    // Create a test group
    $groupId = DB::table('groups')->insertGetId([
        'name' => 'Test Group',
        'referral_code' => 'TEST123',
        'description' => 'Test Description',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Created group with ID: $groupId and referral code: TEST123\n";
    
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
    
    // Test the chat endpoint
    echo "\nTesting chat endpoint...\n";
    
    // Bootstrap Laravel
    $app = require __DIR__.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Create a request
    $request = \Illuminate\Http\Request::create(
        '/chat/send', 
        'POST', 
        [
            'message' => 'Hello from test',
            'group_code' => 'TEST123'
        ],
        [],
        [],
        [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer ' . $userId // Simple auth for testing
        ],
        null,
        [
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ]
    );
    
    // Set the authenticated user
    $user = \App\Models\User::find($userId);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    // Set the authenticated user in the application container
    $app['auth']->setUser($user);
    
    // Handle the request
    $response = $kernel->handle($request);
    
    // Output the response
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
    // Verify the chat was created
    $newChat = DB::table('chats')
        ->where('user_id', $userId)
        ->where('group_id', $groupId)
        ->where('message', 'Hello from test')
        ->first();
    
    if ($newChat) {
        echo "Chat created via endpoint successfully!\n";
    } else {
        echo "Error: Chat not created via endpoint!\n";
    }
    
    // Rollback to clean up
    DB::rollBack();
    
    echo "\nTest completed successfully! (Changes rolled back)\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\nError: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (" . $e->getLine() . ")\n";
    echo "Trace: \n" . $e->getTraceAsString() . "\n";
}

<?php

require __DIR__.'/vendor/autoload.php';

use App\Http\Controllers\ChatController;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Setup database connection
$db = new Illuminate\Database\Capsule\Manager;

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
    echo "=== Starting Chat Controller Test ===\n";
    
    // Create a test user
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'role' => 'member',
    ]);
    
    echo "Created user with ID: {$user->id}\n";
    
    // Create a test group
    $group = Group::create([
        'name' => 'Test Group',
        'referral_code' => 'TEST123',
        'description' => 'Test Description',
    ]);
    
    echo "Created group with ID: {$group->id} and referral code: {$group->referral_code}\n";
    
    // Add user to group
    $user->groups()->attach($group->id);
    echo "Added user to group\n";
    
    // Create a request
    $request = new Request([
        'message' => 'Hello from controller test',
        'group_code' => 'TEST123'
    ]);
    
    // Set the authenticated user using the auth() helper
    $this->actingAs($user);
    
    // Set the user resolver for the request
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    echo "\nCalling ChatController@sendChat...\n";
    
    // Call the controller method directly
    $controller = new ChatController();
    $response = $controller->sendChat($request);
    
    // Get the response data
    $status = $response->getStatusCode();
    $content = json_decode($response->getContent(), true);
    
    echo "Response status: $status\n";
    echo "Response content: " . json_encode($content, JSON_PRETTY_PRINT) . "\n";
    
    // Verify the chat was created
    $chat = DB::table('chats')
        ->where('user_id', $user->id)
        ->where('group_id', $group->id)
        ->where('message', 'Hello from controller test')
        ->first();
    
    if ($chat) {
        echo "\n✅ Chat created successfully!\n";
        echo "Chat ID: {$chat->id}\n";
        echo "Message: {$chat->message}\n";
    } else {
        echo "\n❌ Error: Chat not found in database!\n";
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

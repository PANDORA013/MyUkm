<?php

require __DIR__.'/vendor/autoload.php';

use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illine\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Set up the database connection
$app->make('db');

// Create a test user
$user = User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => Hash::make('password'),
    'role' => 'member',
]);

echo "Created user with ID: " . $user->id . "\n";

// Create a test group
$group = Group::create([
    'name' => 'Test Group',
    'description' => 'Test Description',
    'referral_code' => 'TEST123',
]);

echo "Created group with ID: " . $group->id . " and referral code: " . $group->referral_code . "\n";

// Add user to group
$user->groups()->attach($group->id);

echo "Added user to group\n";

// Create a chat message
$chat = $user->chats()->create([
    'message' => 'Hello World',
    'group_id' => $group->id,
]);

echo "Created chat message with ID: " . $chat->id . "\n";

// Verify the chat was created
$chatExists = \App\Models\Chat::where('id', $chat->id)->exists();
echo "Chat exists in database: " . ($chatExists ? 'Yes' : 'No') . "\n";

// Clean up
$chat->delete();
$user->groups()->detach();
$group->delete();
$user->delete();

echo "Test completed successfully!\n";

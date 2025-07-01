<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class SimpleChatTest extends TestCase
{
    public function testBasicTest()
    {
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            // Create a test user
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'role' => 'member'
            ]);
            
            $this->assertNotNull($user->id, 'User was not created');
            
            // Create a test group
            $group = Group::create([
                'name' => 'Test Group',
                'referral_code' => 'TEST123',
                'description' => 'Test Description'
            ]);
            
            $this->assertNotNull($group->id, 'Group was not created');
            
            // Associate user with group
            $user->groups()->attach($group->id);
            
            // Test the relationship
            $this->assertTrue($user->groups->contains($group->id), 'User is not associated with the group');
            
            // Create a chat message
            $response = $this->actingAs($user)
                ->post(route('chat.send'), [
                    'message' => 'Hello World',
                    'group_code' => 'TEST123'
                ]);
            
            // Check response status
            $response->assertStatus(200);
            
            // Check if the message was saved in the database
            $this->assertDatabaseHas('chats', [
                'user_id' => $user->id,
                'group_id' => $group->id,
                'message' => 'Hello World'
            ]);
            
            echo "Test completed successfully!\n";
            
        } catch (\Exception $e) {
            echo "Test failed: " . $e->getMessage() . "\n";
            throw $e;
        } finally {
            // Rollback the transaction to clean up
            DB::rollBack();
        }
    }
}

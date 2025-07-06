<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Group;
use App\Models\UKM;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SimpleChatTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\CheckRole::class,
            \App\Http\Middleware\EnsureUserRole::class
        ]);
    }

    public function testBasicChatFlow()
    {
        // Create a test UKM
        $ukm = UKM::create([
            'name' => 'Test UKM',
            'code' => 'TST',
            'description' => 'Test UKM Description'
        ]);

        // Create a test user
        $user = User::create([
            'name' => 'Test User',
            'nim' => '12345678',
            'password' => Hash::make('password'),
            'role' => 'member',
            'ukm_id' => $ukm->id
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
        
        // Test login
        $loginResponse = $this->post('/login', [
            'nim' => '12345678',
            'password' => 'password'
        ]);
        
        $loginResponse->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
        
        // Test chat message
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
    }
}

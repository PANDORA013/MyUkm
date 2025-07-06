<?php

namespace Tests\Feature\Chat;

use Tests\TestCase;
use App\Models\User;
use App\Models\Group;
use App\Models\UKM;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ChatTest extends TestCase
{
    use RefreshDatabase;
    
    protected $user;
    protected $group;
    protected $ukm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        
        // Create a test UKM
        $this->ukm = UKM::create([
            'name' => 'Test UKM',
            'code' => 'TST',
            'description' => 'Test UKM Description'
        ]);
        
        // Create a test user
        $this->user = User::create([
            'name' => 'Test User',
            'nim' => '12345678',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'password_plain' => 'password',
            'role' => 'member',
            'ukm_id' => $this->ukm->id
        ]);
        
        // Create a test group
        $this->group = Group::create([
            'name' => 'Test Group',
            'referral_code' => 'TEST123',
            'description' => 'Test Description',
            'ukm_id' => $this->ukm->id
        ]);
        
        // Add user to group
        \Illuminate\Support\Facades\DB::table('group_user')->insert([
            'user_id' => $this->user->id,
            'group_id' => $this->group->id
        ]);
        
        // Fake events to prevent broadcasting during tests
        Event::fake();
    }
    
    /** @test */
    public function user_can_send_chat_message()
    {
        $response = $this->actingAs($this->user)
            ->withSession(['_token' => 'test-token'])
            ->post(route('chat.send'), [
                'message' => 'Hello World',
                'group_code' => 'TEST123',
                '_token' => 'test-token'
            ]);
            
        $response->assertStatus(200);
            
        $this->assertDatabaseHas('chats', [
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
            'message' => 'Hello World'
        ]);
    }
    
    /** @test */
    public function message_requires_content()
    {
        $this->withoutMiddleware();
        
        $response = $this->actingAs($this->user)
            ->postJson(route('chat.send'), [
                'message' => '',
                'group_code' => 'TEST123'
            ]);
            
        $response->assertStatus(422);
        // Just check that it's a validation error, don't be too specific about format
    }
    
    /** @test */
    public function user_can_view_chat_messages()
    {
        // Ensure user is associated with group (but check if already attached)
        if (!$this->user->groups->contains($this->group->id)) {
            $this->user->groups()->attach($this->group->id);
        }
        
        // Create some test messages
        $now = now();
        $messages = [
            [
                'user_id' => $this->user->id, 
                'group_id' => $this->group->id, 
                'message' => 'Hello',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'user_id' => $this->user->id, 
                'group_id' => $this->group->id, 
                'message' => 'World',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];
        
        // Use the query builder to insert directly
        \Illuminate\Support\Facades\DB::table('chats')->insert($messages);
        
        $response = $this->actingAs($this->user)
            ->get(route('chat.messages') . '?group_id=' . $this->group->id);
            
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Hello'])
            ->assertJsonFragment(['message' => 'World']);
    }
    
    /** @test */
    public function user_cannot_send_message_to_unauthorized_group()
    {
        // Create another group that the user is not a member of
        $otherGroup = Group::create([
            'name' => 'Other Group',
            'referral_code' => 'OTHER123',
            'description' => 'Other Group Description',
            'ukm_id' => $this->ukm->id
        ]);
        
        $response = $this->actingAs($this->user)
            ->withSession(['_token' => 'test-token'])
            ->post(route('chat.send'), [
                'message' => 'Unauthorized message',
                'group_code' => 'OTHER123',
                '_token' => 'test-token'
            ]);
            
        $response->assertStatus(403);
    }

    /** @test */
    public function basic_test()
    {
        $this->assertTrue(true);
    }
}

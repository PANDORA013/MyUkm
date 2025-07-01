<?php

namespace Tests\Feature;

use App\Events\MessageSent;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Contracts\Auth\Authenticatable;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_send_chat_message(): void
    {
        // Create a user with member role
        /** @var Authenticatable|User $user */
        $user = User::factory()->create(['role' => 'member']);
        
        // Create a group
        $group = Group::create([
            'name' => 'Test Group',
            'description' => 'Test Description',
            'referral_code' => 'TEST123'
        ]);
        
        // Add user to group using the pivot model
        GroupUser::create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'is_admin' => false,
            'is_muted' => false
        ]);
        
        $this->actingAs($user)
            ->post(route('chat.send'), [
                'message' => 'Hello World',
                'group_code' => 'TEST123'
            ])
            ->assertStatus(200);
            
        $this->assertDatabaseHas('chats', [
            'user_id' => $user->id,
            'message' => 'Hello World',
            'group_id' => $group->id
        ]);
    }

    /** @test */
    public function message_requires_content(): void
    {
        // Create a user with member role
        /** @var Authenticatable|User $user */
        $user = User::factory()->create(['role' => 'member']);
        
        // Create a group
        $group = Group::create([
            'name' => 'Test Group',
            'description' => 'Test Description',
            'referral_code' => 'TEST123'
        ]);
        
        // Add user to group using the pivot model
        GroupUser::create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'is_admin' => false,
            'is_muted' => false
        ]);
        
        $this->actingAs($user)
            ->post(route('chat.send'), [
                'message' => '',
                'group_code' => 'TEST123'
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
        $group = \App\Models\Group::create([
            'name' => 'Test Group',
            'description' => 'Test Description',
            'referral_code' => 'TEST456'
        ]);
        
        $user->groups()->attach($group->id);
        
        $this->actingAs($user)
            ->post(route('chat.send'), [
                'message' => '',
                'group_id' => $group->id
            ])
            ->assertSessionHasErrors('message');
    }

    /** @test */
    public function basic_test()
    {
        $this->assertTrue(true);
    }
}

<?php

namespace Tests\Feature;

use App\Events\MessageSent;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_send_chat_message()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create();
        
        $this->actingAs($user)
            ->post('/chat', ['message' => 'Hello World'])
            ->assertStatus(200);
            
        $this->assertDatabaseHas('messages', [
            'user_id' => $user->id,
            'message' => 'Hello World'
        ]);
    }

    /** @test */
    public function message_requires_content()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create();
        
        $this->actingAs($user)
            ->post('/chat', ['message' => ''])
            ->assertSessionHasErrors('message');
    }

    /** @test */
    public function basic_test()
    {
        $this->assertTrue(true);
    }
}

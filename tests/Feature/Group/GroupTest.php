<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Group;
use PHPUnit\Framework\Attributes\Test;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_login_with_correct_nim_and_password()
    {
        $user = User::factory()->create([
            'nim' => '12345678',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'nim' => '12345678',
            'password' => 'password',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_can_join_group_with_valid_code()
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // pastikan instance User
        $group = Group::factory()->create(['referral_code' => '0812']);

        $response = $this->actingAs($user)->post('/ukm/join', [
            'group_code' => '0812',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('group_user', [
            'user_id' => $user->id,
            'group_id' => $group->id,
        ]);
    }

    #[Test]
    public function user_cannot_join_group_with_invalid_code()
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // pastikan instance User

        $response = $this->actingAs($user)->post('/ukm/join', [
            'group_code' => '9999',
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('group_user', [
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function user_can_leave_group()
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // pastikan instance User
        $group = Group::factory()->create();
        $user->groups()->attach($group->id); // Join dulu

        $response = $this->actingAs($user)->delete("/ukm/{$group->referral_code}/leave");

        $response->assertRedirect();
        $this->assertDatabaseMissing('group_user', [
            'user_id' => $user->id,
            'group_id' => $group->id,
        ]);
    }
}

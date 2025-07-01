<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Contracts\Auth\Authenticatable;

class UkmJoinTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_join_ukm_with_valid_referral_code(): void
    {
        // Create a test group
        $group = Group::factory()->create([
            'name' => 'UKM Test',
            'referral_code' => 'ABCD',
            'is_active' => true
        ]);

        // Create and login a user
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user, 'web');

        // Join the group
        $response = $this->post(route('ukm.join'), [
            'group_code' => 'ABCD'
        ]);

        // Assert the user was redirected back with success message
        $response->assertRedirect(route('ukm.index'));
        $response->assertSessionHas('success', 'Berhasil bergabung dengan UKM Test');

        // Assert the user is now a member of the group
        $this->assertTrue($user->groups()->where('groups.id', $group->id)->exists());
    }

    #[Test]
    public function user_cannot_join_with_invalid_referral_code(): void
    {
        // Create and login a user
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user, 'web');

        // Try to join with invalid code
        $response = $this->post(route('ukm.join'), [
            'group_code' => 'INVALID'
        ]);

        // Assert error message
        $response->assertRedirect(route('ukm.index'));
        $response->assertSessionHas('error', 'Kode referral tidak valid');

        // Assert the user is not a member of any group
        $this->assertCount(0, $user->groups);
    }

    #[Test]
    public function user_cannot_join_same_ukm_twice(): void
    {
        // Create a test group
        $group = Group::factory()->create([
            'referral_code' => 'ABCD',
            'is_active' => true
        ]);

        // Create and login a user
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user, 'web');

        // Join the group first time
        $this->post(route('ukm.join'), ['group_code' => 'ABCD']);

        // Try to join the same group again
        $response = $this->post(route('ukm.join'), [
            'group_code' => 'ABCD'
        ]);

        // Assert info message
        $response->assertRedirect(route('ukm.index'));
        $response->assertSessionHas('info', 'Anda sudah tergabung di UKM ini');

        // Assert the user is still only a member once
        $this->assertCount(1, $user->groups);
    }

    #[Test]
    public function user_can_leave_ukm(): void
    {
        // Create a test group
        $group = Group::factory()->create([
            'name' => 'UKM Test',
            'referral_code' => 'ABCD',
            'is_active' => true
        ]);

        // Create and login a user
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user, 'web');

        // Join the group
        $user->groups()->attach($group->id);

        // Leave the group
        $response = $this->delete(route('ukm.leave', ['code' => 'ABCD']));

        // Assert success message
        $response->assertRedirect(route('ukm.index'));
        $response->assertSessionHas('success', 'Berhasil keluar dari UKM Test');

        // Assert the user is no longer a member
        $this->assertFalse($user->groups()->where('groups.id', $group->id)->exists());
    }

    #[Test]
    public function guest_cannot_access_ukm_pages(): void
    {
        // Try to access UKM index without logging in
        $response = $this->get(route('ukm.index'));
        $response->assertRedirect(route('login'));

        // Try to join a group without logging in
        $response = $this->post(route('ukm.join'), ['group_code' => 'ABCD']);
        $response->assertRedirect(route('login'));

        // Try to leave a group without logging in
        $response = $this->delete(route('ukm.leave', ['code' => 'ABCD']));
        $response->assertRedirect(route('login'));
    }
}

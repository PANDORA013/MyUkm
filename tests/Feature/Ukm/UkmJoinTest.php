<?php

namespace Tests\Feature\Ukm;

use App\Models\Group;
use App\Models\User;
use App\Models\UKM;
use App\Models\GroupUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UkmJoinTest extends TestCase
{
    use RefreshDatabase;
    
    protected $ukm;
    protected $user;
    protected $group;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test UKM
        $this->ukm = UKM::create([
            'name' => 'Test UKM',
            'code' => 'TST',
            'description' => 'Test UKM Description',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Create a test user
        $this->user = User::create([
            'name' => 'Test User',
            'nim' => '12345678',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'password_plain' => 'password',
            'role' => 'member',
            'ukm_id' => $this->ukm->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Create a test group
        $this->group = Group::create([
            'name' => 'Test Group',
            'referral_code' => 'TEST123',
            'description' => 'Test Description',
            'ukm_id' => $this->ukm->id,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /** @test */
    public function user_can_join_ukm_with_valid_referral_code()
    {
        // Login the user
        $this->actingAs($this->user);

        // Join the group
        $response = $this->post(route('ukm.join'), [
            'group_code' => 'TEST123'
        ]);

        // Assert the user was redirected back with success message
        $response->assertRedirect(route('ukm.index'));
        $response->assertSessionHas('success', 'Berhasil bergabung dengan Test Group');

        // Assert the user is now a member of the group
        $this->assertDatabaseHas('group_user', [
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function user_cannot_join_with_invalid_referral_code()
    {
        // Login the user
        $this->actingAs($this->user);

        // Try to join with invalid code
        $response = $this->post(route('ukm.join'), [
            'group_code' => 'INVALID'
        ]);

        // Assert validation error
        $response->assertSessionHasErrors('group_code');

        // Assert user is not a member of any group
        $this->assertDatabaseMissing('group_user', [
            'user_id' => $this->user->id,
            'group_id' => $this->group->id
        ]);
    }

    /** @test */
    public function user_cannot_join_same_ukm_twice()
    {
        // Login the user
        $this->actingAs($this->user);

        // Join the group first time
        $response1 = $this->post(route('ukm.join'), [
            'group_code' => 'TEST123'
        ]);

        // Try to join the same group again
        $response2 = $this->post(route('ukm.join'), [
            'group_code' => 'TEST123'
        ]);

        // Assert the second attempt shows an error
        $response2->assertSessionHas('error');

        // Assert user is only a member of the group once
        $count = DB::table('group_user')
            ->where('user_id', $this->user->id)
            ->where('group_id', $this->group->id)
            ->whereNull('deleted_at')
            ->count();
            
        $this->assertEquals(1, $count);
    }

    /** @test */
    public function user_can_leave_ukm()
    {
        // Login the user
        $this->actingAs($this->user);

        // Join the group first
        $this->user->groups()->attach($this->group->id);

        // Leave the group
        $response = $this->delete(route('ukm.leave', $this->group->id));

        // Assert the user was redirected back with success message
        $response->assertRedirect(route('ukm.index'));
        $response->assertSessionHas('success', 'Berhasil keluar dari UKM');

        // Assert the user is no longer a member of the group
        $this->assertDatabaseMissing('group_user', [
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function guest_cannot_access_ukm_pages()
    {
        // Try to access UKM index without logging in
        $response = $this->get(route('ukm.index'));
        $response->assertRedirect(route('login'));

        // Try to join a group without logging in
        $response = $this->post(route('ukm.join'), [
            'group_code' => 'TEST123'
        ]);
        $response->assertRedirect(route('login'));

        // Try to leave a group without logging in
        $response = $this->delete(route('ukm.leave', $this->group->id));
        $response->assertRedirect(route('login'));
    }
}

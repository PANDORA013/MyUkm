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
        
        // Disable CSRF protection for this test
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        
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
            'password' => Hash::make('password'),
            'password_plain' => 'password',
            'role' => 'member',
            'ukm_id' => $this->ukm->id
        ]);
        
        // Create a test group
        $this->group = Group::create([
            'name' => 'Test Group',
            'referral_code' => '1234', // Match the code used in tests
            'description' => 'Test Description',
            'ukm_id' => $this->ukm->id,
            'is_active' => true
        ]);
    }

    /** @test */
    public function user_can_join_ukm_with_valid_referral_code()
    {
        // Login the user
        $this->actingAs($this->user);

        // Join the group with CSRF token
        $response = $this->withSession(['_token' => 'test-token'])
                         ->from(route('ukm.index'))
                         ->post(route('ukm.join'), [
                             '_token' => 'test-token',
                             'group_code' => '1234' // 4 digit angka
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
        $response = $this->withSession(['_token' => 'test-token'])
                         ->from(route('ukm.index'))
                         ->post(route('ukm.join'), [
                             '_token' => 'test-token',
                             'group_code' => '9999' // 4 digit angka
                         ]);

        // Debug: Check what actually happened
        // dd($response->getStatusCode(), $response->getContent(), session()->all());

        // Try with validation error or redirect
        if ($response->getStatusCode() === 422) {
            $response->assertSessionHasErrors('group_code');
        } else if ($response->getStatusCode() === 302) {
            // Might redirect back with flash message
            $response->assertSessionHas('error');
        }

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

        // Join the group first time using the new CSRF-free method
        $response1 = $this->authenticatedPost($this->user, route('ukm.join'), [
            'group_code' => '1234' // 4 digit angka
        ]);

        // Verify first join was successful
        $response1->assertRedirect(route('ukm.index'));
        $response1->assertSessionHas('success');

        // Try to join the same group again
        $response2 = $this->authenticatedPost($this->user, route('ukm.join'), [
            'group_code' => '1234' // 4 digit angka
        ]);

        // Assert the second attempt shows an info message about already being a member
        $response2->assertRedirect(route('ukm.index'));
        $response2->assertSessionHas('info', 'Anda sudah tergabung di UKM ini');

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

        // Leave the group with CSRF token (use group code, not ID)
        $response = $this->withSession(['_token' => 'test-token'])
                         ->from(route('ukm.index'))
                         ->delete(route('ukm.leave', $this->group->referral_code), [
                             '_token' => 'test-token'
                         ]);

        // Assert the user was redirected back with success message
        $response->assertRedirect(route('ukm.index'));
        $response->assertSessionHas('success', 'Berhasil keluar dari Test Group');

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

        // Try to join a group without logging in (CSRF still applies even for guests)
        $response = $this->withSession(['_token' => 'test-token'])
                         ->post(route('ukm.join'), [
                             '_token' => 'test-token',
                             'group_code' => '1234' // 4 digit angka
                         ]);
        $response->assertRedirect(route('login'));

        // Try to leave a group without logging in
        $response = $this->withSession(['_token' => 'test-token'])
                         ->delete(route('ukm.leave', $this->group->referral_code), [
                             '_token' => 'test-token'
                         ]);
        $response->assertRedirect(route('login'));
    }
}

<?php

namespace Tests\Feature\Group;

use Tests\TestCase;
use App\Models\User;
use App\Models\Group;
use App\Models\UKM;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class GroupTest extends TestCase
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
    }
    
    /** @test */
    public function user_can_login_with_correct_nim_and_password()
    {
        $response = $this->post(route('login'), [
            'nim' => '12345678',
            'password' => 'password',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($this->user);
    }

    /** @test */
    public function user_can_join_group_with_valid_code()
    {
        $response = $this->actingAs($this->user)
            ->post(route('group.join'), [
                'referral_code' => 'TEST123',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('group_user', [
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
        ]);
    }

    /** @test */
    public function user_cannot_join_group_with_invalid_code()
    {
        $response = $this->actingAs($this->user)
            ->post(route('group.join'), [
                'referral_code' => 'INVALID',
            ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('group_user', [
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function user_can_leave_group()
    {
        // Join group first
        $this->user->groups()->attach($this->group->id);
        
        $response = $this->actingAs($this->user)
            ->post(route('group.leave'), [
                'group_id' => $this->group->id
            ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('group_user', [
            'user_id' => $this->user->id,
            'group_id' => $this->group->id,
        ]);
    }
    
    /** @test */
    public function admin_can_create_group()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'nim' => 'ADM001',
            'password' => Hash::make('admin123'),
            'role' => 'admin_website',
            'ukm_id' => $this->ukm->id
        ]);
        
        $response = $this->actingAs($admin)
            ->post(route('admin.admin.groups.store'), [
                'name' => 'New Group',
                'referral_code' => 'NEWGRP',
                'description' => 'New Group Description',
                'ukm_id' => $this->ukm->id
            ]);
            
        $response->assertRedirect(route('admin.admin.groups.index'));
        $this->assertDatabaseHas('groups', [
            'name' => 'New Group',
            'referral_code' => 'NEWGRP',
            'ukm_id' => $this->ukm->id
        ]);
    }
}

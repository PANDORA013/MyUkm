<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\UKM;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminTest extends TestCase
{
    use RefreshDatabase;
    
    protected $admin;
    protected $user;
    protected $ukm;
    protected $group;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        
        // Create a test UKM
        $this->ukm = UKM::create([
            'name' => 'Test UKM',
            'code' => 'TST',
            'description' => 'Test UKM Description',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'nim' => 'ADM001',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'password_plain' => 'admin123',
            'role' => 'admin_website',
            'ukm_id' => $this->ukm->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Create regular user
        $this->user = User::create([
            'name' => 'Regular User',
            'nim' => 'USR001',
            'email' => 'user@test.com',
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
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    /** @test */
    public function admin_can_view_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));
            
        $response->assertStatus(200);
    }
    
    /** @test */
    public function admin_can_view_users_list()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));
            
        $response->assertStatus(200)
            ->assertViewHas('users');
    }
    
    /** @test */
    public function admin_can_create_user()
    {
        $userData = [
            'name' => 'New User',
            'nim' => 'USR002',
            'email' => 'newuser@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'member',
            'ukm_id' => $this->ukm->id,
            'password_plain' => 'password'
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), $userData);
            
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'nim' => 'USR002',
            'email' => 'newuser@test.com',
            'role' => 'member',
            'ukm_id' => $this->ukm->id
        ]);
        
        // Verify password was hashed
        $user = User::where('nim', 'USR002')->first();
        $this->assertTrue(Hash::check('password', $user->password));
    }
    
    /** @test */
    public function admin_can_update_user()
    {
        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $this->user->id), [
                'name' => 'Updated Name',
                'nim' => 'USR001',
                'role' => 'member',
                'ukm_id' => $this->ukm->id
            ]);
            
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name'
        ]);
    }
    
    /** @test */
    public function admin_can_delete_user()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $this->user->id));
            
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', [
            'id' => $this->user->id
        ]);
    }
    
    /** @test */
    public function admin_can_manage_groups()
    {
        // View groups
        $response = $this->actingAs($this->admin)
            ->get(route('admin.groups.index'));
        $response->assertStatus(200);
        
        // Create group
        $response = $this->actingAs($this->admin)
            ->post(route('admin.groups.store'), [
                'name' => 'New Group',
                'referral_code' => 'NEWGRP',
                'description' => 'New Group Description',
                'ukm_id' => $this->ukm->id
            ]);
        $response->assertRedirect(route('admin.groups.index'));
        $this->assertDatabaseHas('groups', [
            'name' => 'New Group',
            'referral_code' => 'NEWGRP'
        ]);
    }
    
    /** @test */
    public function non_admin_cannot_access_admin_routes()
    {
        $routes = [
            route('admin.dashboard'),
            route('admin.users.index'),
            route('admin.groups.index')
        ];
        
        foreach ($routes as $route) {
            $response = $this->actingAs($this->user)->get($route);
            $response->assertStatus(403);
        }
    }
}

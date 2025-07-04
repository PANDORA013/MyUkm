<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\UKM;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    protected $ukm;
    protected $admin;
    protected $user;

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
        
        // Create a test admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'nim' => 'ADM001',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin_website',
            'ukm_id' => $this->ukm->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Create a test regular user
        $this->user = User::create([
            'name' => 'Test User',
            'nim' => 'USR001',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'ukm_id' => $this->ukm->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    /** @test */
    public function user_can_register()
    {
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/register', [
                'name' => 'New User',
                'nim' => '12345678',
                'email' => 'newuser@test.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'ukm_code' => 'TST',
                '_token' => 'test-token'
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'nim' => '12345678',
            'email' => 'newuser@test.com',
            'role' => 'member',
            'ukm_id' => $this->ukm->id
        ]);
    }

    /** @test */
    public function user_can_login()
    {
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/login', [
                'nim' => 'USR001',
                'password' => 'password',
                '_token' => 'test-token'
            ]);

        $response->assertStatus(302);
        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($this->user);
    }

    /** @test */
    public function guest_cannot_access_dashboard()
    {
        $response = $this->get('/home');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function user_cannot_login_with_wrong_password()
    {
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/login', [
                'nim' => 'USR001',
                'password' => 'wrongpassword',
                '_token' => 'test-token'
            ]);
        
        $response->assertStatus(302);
        $response->assertSessionHasErrors('nim');
        $this->assertGuest();
    }
    
    /** @test */
    public function admin_can_view_all_members()
    {
        $this->actingAs($this->admin);
        
        $response = $this->get(route('admin.admin.users.index'));
        $response->assertStatus(200);
        $response->assertViewHas('users');
    }
    
    /** @test */
    public function non_admin_cannot_view_all_members()
    {
        $this->actingAs($this->user);
        
        $response = $this->get(route('admin.admin.users.index'));
        $response->assertStatus(403);
    }
}

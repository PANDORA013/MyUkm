<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\UKM;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    
    protected $ukm;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
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
            'role' => 'member',
            'ukm_id' => $this->ukm->id
        ]);
    }
    
    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $response = $this->post('/login', [
            'nim' => '12345678',
            'password' => 'password',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($this->user);
    }
    
    /** @test */
    public function user_cannot_login_with_incorrect_password()
    {
        $response = $this->post('/login', [
            'nim' => '12345678',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('nim');
        $this->assertGuest();
    }
    
    /** @test */
    public function user_can_register_with_valid_data()
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'nim' => '87654321',
            'password' => 'password',
            'password_confirmation' => 'password',
            'ukm_code' => 'TST'
        ]);

        $response->assertRedirect('/home');
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'nim' => '87654321',
            'ukm_id' => $this->ukm->id
        ]);
    }
    
    /** @test */
    public function user_cannot_register_with_existing_nim()
    {
        $response = $this->post('/register', [
            'name' => 'Duplicate NIM',
            'nim' => '12345678', // Already exists
            'password' => 'password',
            'password_confirmation' => 'password',
            'ukm_code' => 'TST'
        ]);

        $response->assertSessionHasErrors('nim');
    }
    
    /** @test */
    public function user_can_logout()
    {
        $response = $this->actingAs($this->user)
            ->post('/logout');
            
        $response->assertRedirect('/');
        $this->assertGuest();
    }
    
    /** @test */
    public function authenticated_user_cannot_see_login_page()
    {
        $response = $this->actingAs($this->user)
            ->get('/login');
            
        $response->assertRedirect('/home');
    }
    
    /** @test */
    public function authenticated_user_cannot_see_register_page()
    {
        $response = $this->actingAs($this->user)
            ->get('/register');
            
        $response->assertRedirect('/home');
    }
}

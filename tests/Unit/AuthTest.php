<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UKM;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
    protected $ukm;
    protected $group;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable CSRF protection for auth tests
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        
        // Create a test UKM
        $this->ukm = UKM::create([
            'name' => 'Test UKM',
            'code' => 'TST',
            'description' => 'Test UKM Description'
        ]);
        
        // Create corresponding group
        $this->group = Group::create([
            'name' => 'Test UKM',
            'referral_code' => 'TST',
        ]);
    }

    /** @test */
    public function user_can_register()
    {
        $userData = [
            '_token' => 'test-token',
            'name' => 'Test User',
            'nim' => '12345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'ukm_code' => 'TST',
        ];

        $response = $this->withSession(['_token' => 'test-token'])
                         ->from('/register')
                         ->post('/register', $userData);
        
        $response->assertStatus(302);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'nim' => '12345678',
        ]);
    }

    /** @test */
    public function user_can_login()
    {
        // Create user first
        $user = User::create([
            'name' => 'Test User',
            'nim' => '12345678',
            'password' => Hash::make('password123'),
            'role' => 'anggota',
        ]);

        // Login with CSRF token
        $response = $this->withSession(['_token' => 'test-token'])
                         ->from('/login')
                         ->post('/login', [
                             '_token' => 'test-token',
                             'nim' => $user->nim,
                             'password' => 'password123',
                         ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::create([
            'name' => 'Test User',
            'nim' => '12345678',
            'password' => Hash::make('password123'),
            'role' => 'anggota',
        ]);

        $response = $this->withSession(['_token' => 'test-token'])
                         ->from('/login')
                         ->post('/login', [
                             '_token' => 'test-token',
                             'nim' => $user->nim,
                             'password' => 'wrong-password',
                         ]);

        $response->assertSessionHasErrors('nim');
        $this->assertGuest();
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::create([
            'name' => 'Test User',
            'nim' => '12345678',
            'password' => Hash::make('password123'),
            'role' => 'anggota',
        ]);

        $response = $this->actingAs($user)
                         ->withSession(['_token' => 'test-token'])
                         ->from('/home')
                         ->post('/logout', [
                             '_token' => 'test-token'
                         ]);

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}

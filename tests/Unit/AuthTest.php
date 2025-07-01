<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UKM;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
    protected $ukm;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test UKM
        $this->ukm = UKM::create([
            'name' => 'Test UKM',
            'code' => 'TST',
            'description' => 'Test UKM Description'
        ]);
    }

    /** @test */
    public function user_can_register()
    {
        $userData = [
            'name' => 'Test User',
            'nim' => '12345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'ukm_code' => 'TST',
        ];

        $response = $this->post('/register', $userData);
        
        $response->assertStatus(302);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'nim' => '12345678',
            'role' => 'member',
            'ukm_id' => $this->ukm->id,
        ]);
    }

    /** @test */
    public function user_can_login()
    {
        $user = User::create([
            'name' => 'Test User',
            'nim' => '12345678',
            'password' => Hash::make('password123'),
            'role' => 'member',
            'ukm_id' => $this->ukm->id,
        ]);

        $response = $this->post('/login', [
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
            'role' => 'member',
            'ukm_id' => $this->ukm->id,
        ]);

        $response = $this->post('/login', [
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
            'role' => 'member',
            'ukm_id' => $this->ukm->id,
        ]);

        $response = $this->actingAs($user)
            ->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}

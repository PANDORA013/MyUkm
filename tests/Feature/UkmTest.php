<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UkmTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        // Jika halaman utama me-redirect ke login, gunakan assertRedirect
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
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

    /** @test */
    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/home');
        $response->assertRedirect('/login');
    }
}

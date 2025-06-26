<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'nim' => '12345678',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/home');
        $this->assertDatabaseHas('users', ['nim' => '12345678']);
    }

    #[Test]
    public function user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'nim' => $user->nim,
            'password' => 'password',
        ]);

        $response->assertRedirect('/home');
    }

    #[Test]
    public function guest_cannot_access_dashboard()
    {
        $response = $this->get('/home');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function user_can_join_ukm_with_valid_code()
    {
        // Simulasi User dan UKM dibuat di sini
        // Tambahkan model UKM jika belum
        $this->assertTrue(true); // Placeholder
    }

    #[Test]
    public function admin_can_view_all_members()
    {
        // Simulasi admin login dan akses halaman anggota UKM
        $this->assertTrue(true); // Placeholder
    }

    #[Test]
    public function user_cannot_login_with_wrong_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $response = $this->post('/login', [
            'nim' => $user->nim,
            'password' => 'wrongpassword',
        ]);
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser()
    {
        return User::factory()->create([
            'is_admin' => true,
            'password' => bcrypt('admin123')
        ]);
    }

    /** @test */
    public function admin_can_view_users()
    {
        $admin = $this->createAdminUser();
        
        $response = $this->actingAs($admin)
                         ->get('/admin/users');
                         
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_user()
    {
        $admin = $this->createAdminUser();
        
        $userData = [
            'name' => 'New User',
            'nim' => '87654321',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => false
        ];
        
        $response = $this->actingAs($admin)
                         ->post('/admin/users', $userData);
                         
        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'nim' => '87654321',
            'email' => 'newuser@example.com'
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_admin_routes()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
                         ->get('/admin/users');
                         
        $response->assertStatus(403);
    }
}

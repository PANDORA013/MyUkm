<?php

namespace Tests\Feature;

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminUser(array $attributes = [])
    {
        return parent::createAdminUser(array_merge([
            'password' => bcrypt('admin123')
        ], $attributes));
    }

    /** @test */
    public function admin_can_view_users()
    {
        /** @var \App\Models\User $admin */
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
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
                         ->get('/admin/users');
                         
        $response->assertStatus(403);
    }
}

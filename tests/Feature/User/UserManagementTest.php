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

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    }

    protected function createAdminUser(array $attributes = [])
    {
        return parent::createAdminUser(array_merge([
            'password' => bcrypt('admin123'),
            'role' => 'admin_website'
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
        $this->withoutMiddleware();
        
        $admin = $this->createAdminUser();
        
        $userData = [
            'name' => 'New User',
            'nim' => '87654321',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'member',
            'ukm_id' => null
        ];
        
        $response = $this->actingAs($admin)
                         ->post('/admin/users', $userData);
                         
        $response->assertRedirect();
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
                         
        // Admin middleware redirects non-admin users (302) as per middleware design
        $response->assertStatus(302);
    }
}

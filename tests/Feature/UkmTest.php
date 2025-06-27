<?php

namespace Tests\Feature;

use App\Models\Ukm;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UkmTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function createAdminUser(): Authenticatable
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create([
            'is_admin' => true,
            'password' => bcrypt('admin123')
        ]);
        
        return $user;
    }

    /** @test */
    public function admin_can_view_ukm_list()
    {
        $admin = $this->createAdminUser();
        
        $response = $this->actingAs($admin)
                         ->get('/admin/ukm');
                         
        $response->assertStatus(200);
        $response->assertViewHas('ukms');
    }

    /** @test */
    public function admin_can_create_ukm()
    {
        $admin = $this->createAdminUser();
        Storage::fake('public');
        
        $data = [
            'name' => 'UKM Test',
            'description' => 'Deskripsi UKM Test',
            'pembina' => 'Nama Pembina',
            'logo' => UploadedFile::fake()->image('ukm-logo.jpg'),
        ];
        
        $response = $this->actingAs($admin)
                         ->post('/admin/ukm', $data);
                         
        $response->assertRedirect('/admin/ukm');
        $this->assertDatabaseHas('ukms', [
            'name' => 'UKM Test',
            'description' => 'Deskripsi UKM Test',
            'pembina' => 'Nama Pembina',
        ]);
        
        // Verify the file was stored (using assertTrue instead of assertExists)
        $this->assertTrue(
            Storage::disk('public')->exists('ukm-logos/' . $data['logo']->hashName()),
            'The logo file was not stored.'
        );
    }

    /** @test */
    public function user_can_view_ukm_list()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
                         ->get('/ukm');
                         
        $response->assertStatus(200);
        $response->assertViewHas('ukms');
    }

    /** @test */
    public function user_can_join_ukm()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $ukm = Ukm::factory()->create();
        
        $response = $this->actingAs($user)
                         ->post("/ukm/{$ukm->id}/join");
                         
        $response->assertRedirect();
        $this->assertDatabaseHas('ukm_user', [
            'user_id' => $user->id,
            'ukm_id' => $ukm->id,
        ]);
    }

    /** @test */
    public function user_can_leave_ukm()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $ukm = Ukm::factory()->create();
        $user->ukms()->attach($ukm->id);
        
        $response = $this->actingAs($user)
                         ->delete("/ukm/{$ukm->id}/leave");
                         
        $response->assertRedirect();
        $this->assertDatabaseMissing('ukm_user', [
            'user_id' => $user->id,
            'ukm_id' => $ukm->id,
        ]);
    }

    /** @test */
    public function admin_can_delete_ukm()
    {
        $admin = $this->createAdminUser();
        $ukm = Ukm::factory()->create();
        
        $response = $this->actingAs($admin)
                         ->delete("/admin/ukm/{$ukm->id}");
                         
        $response->assertRedirect('/admin/ukm');
        $this->assertDatabaseMissing('ukms', ['id' => $ukm->id]);
    }

    /** @test */
    public function non_admin_cannot_access_admin_ukm_routes()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $ukm = Ukm::factory()->create();
        
        $routes = [
            ['method' => 'get', 'url' => '/admin/ukm/create'],
            ['method' => 'post', 'url' => '/admin/ukm'],
            ['method' => 'delete', 'url' => "/admin/ukm/{$ukm->id}"],
        ];
        
        foreach ($routes as $route) {
            $response = $this->actingAs($user)
                            ->{$route['method']}($route['url']);
            $response->assertStatus(403);
        }
    }
}

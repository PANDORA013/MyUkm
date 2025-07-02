<?php

namespace Tests\Feature\Ukm;

use App\Models\UKM;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UkmTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    protected $admin;
    protected $user;
    protected $ukm;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test UKM first
        $this->ukm = UKM::create([
            'name' => 'Test UKM',
            'code' => 'TST',
            'description' => 'Test UKM Description',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'nim' => 'ADM001',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'password_plain' => 'admin123',
            'role' => 'admin_website',
            'ukm_id' => $this->ukm->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Create regular user
        $this->user = User::create([
            'name' => 'Regular User',
            'nim' => 'USR001',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'password_plain' => 'password',
            'role' => 'member',
            'ukm_id' => $this->ukm->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /** @test */
    public function admin_can_view_ukm_list()
    {
        $response = $this->actingAs($this->admin)
                       ->get(route('admin.ukm.index'));
                         
        $response->assertStatus(200);
        $response->assertViewHas('ukms');
    }

    /** @test */
    public function admin_can_create_ukm()
    {
        Storage::fake('public');
        
        $data = [
            'name' => 'New UKM',
            'code' => 'NEW',
            'description' => 'New UKM Description',
            'logo' => UploadedFile::fake()->image('logo.jpg'),
        ];
        
        $response = $this->actingAs($this->admin)
                        ->post(route('admin.ukm.store'), $data);
                         
        $response->assertRedirect(route('admin.ukm.index'));
        $this->assertDatabaseHas('ukms', [
            'name' => 'New UKM',
            'code' => 'NEW',
            'description' => 'New UKM Description',
        ]);
    }

    /** @test */
    public function user_can_view_ukm_list()
    {
        $response = $this->actingAs($this->user)
                       ->get(route('ukm.index'));
                         
        $response->assertStatus(200);
        $response->assertViewHas('ukms');
    }

    /** @test */
    public function user_can_join_ukm()
    {
        $response = $this->actingAs($this->user)
                       ->post(route('ukm.join', $this->ukm->id));
                         
        $response->assertRedirect();
        $this->assertDatabaseHas('group_user', [
            'user_id' => $this->user->id,
            'group_id' => $this->ukm->groups()->first()->id,
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

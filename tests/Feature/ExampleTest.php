<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\UKM;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test that unauthenticated users are redirected to login.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Test that authenticated user can access dashboard.
     */
    public function test_authenticated_user_can_access_dashboard()
    {
        // Create user with UKM automatically via factory
        $user = User::factory()->create();
        
        // Test direct access to /ukm instead of /home
        $response = $this->actingAs($user)->get('/ukm');
        $response->assertStatus(200);
    }
}

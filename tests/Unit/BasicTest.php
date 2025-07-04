<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BasicTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_basic_example()
    {
        $this->assertTrue(true);
    }

    /**
     * Test that the application returns a successful response.
     *
     * @return void
     */
    public function test_application_returns_successful_response()
    {
        // The root route redirects unauthenticated users, so we expect a 302
        $response = $this->get('/');
        $response->assertRedirect(); // This should pass with a 302 redirect
    }
}

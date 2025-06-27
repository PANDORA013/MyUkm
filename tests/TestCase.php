<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations and seeders before each test
        Artisan::call('migrate:fresh');
        Artisan::call('db:seed');
    }

    /**
     * Create an admin user for testing
     */
    protected function createAdminUser()
    {
        return \App\Models\User::factory()->create([
            'is_admin' => true,
            'password' => bcrypt('password')
        ]);
    }

    /**
     * Create a regular user for testing
     */
    protected function createUser()
    {
        return \App\Models\User::factory()->create([
            'is_admin' => false,
            'password' => bcrypt('password')
        ]);
    }
}

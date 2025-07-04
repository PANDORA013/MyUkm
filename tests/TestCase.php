<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\TestHelpers\TestResponseMacros;

/**
 * Base test case for all tests
 * 
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase, WithFaker;

    /**
     * Indicates whether the default seeder should run before each test.
     */
    protected bool $seed = true;

    /**
     * The base URL to use while testing the application.
     */
    protected string $baseUrl = 'http://localhost';

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Show all errors in test environment
        $this->withoutExceptionHandling();
        
        // Disable CSRF middleware for testing
        $this->withoutMiddleware([
            \App\Http\Middleware\VerifyCsrfToken::class,
        ]);
        
        // Ensure we're using the testing database
        $this->configureDatabase();
        
        // Register test response macros
        if (!TestResponse::hasMacro('assertSuccess')) {
            TestResponseMacros::register();
        }
    }

    /**
     * Configure database for testing
     */
    protected function configureDatabase(): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => env('DB_DATABASE', 'myukm_test'),
            'database.connections.mysql.username' => env('DB_USERNAME', 'root'),
            'database.connections.mysql.password' => env('DB_PASSWORD', ''),
        ]);
    }

    /**
     * Create an admin user for testing
     * 
     * @param array $attributes Additional user attributes
     * @return \App\Models\User
     */
    protected function createAdminUser(array $attributes = [])
    {
        return $this->createUser(array_merge([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ], $attributes));
    }

    /**
     * Create a regular user for testing
     * 
     * @param array $attributes Additional user attributes
     * @return \App\Models\User
     */
    protected function createUser(array $attributes = [])
    {
        return \App\Models\User::factory()->create(array_merge([
            'name' => 'Test User',
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'is_admin' => false,
        ], $attributes));
    }
    
    /**
     * Assert that a given where condition exists in the database
     * 
     * @param string $table
     * @param array $data
     * @param string $connection
     * @return $this
     */
    protected function assertDatabaseHasRow(string $table, array $data, string $connection = null)
    {
        $this->assertDatabaseHas($table, $data, $connection);
        return $this;
    }
}

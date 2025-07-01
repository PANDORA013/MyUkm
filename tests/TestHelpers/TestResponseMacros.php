<?php

namespace Tests\TestHelpers;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;

// Add PHPDoc to help IDE understand the macros
/**
 * @mixin \Illuminate\Testing\TestResponse
 */
class TestResponseMacros
{
    /**
     * Register the test response macros
     */
    public static function register()
    {
        // Register a simple success assertion
        TestResponse::macro('assertSuccess', function () {
            /** @var \Illuminate\Testing\TestResponse $this */
            $status = $this->status();
            PHPUnit::assertTrue(
                $status >= 200 && $status < 300,
                'Expected status code 2xx, received: ' . $status
            );
            return $this;
        });

        // Register JSON success assertion
        TestResponse::macro('assertJsonSuccess', function ($data = null) {
            /** @var \Illuminate\Testing\TestResponse $this */
            $this->assertOk();
            
            $response = $this->json();
            
            PHPUnit::assertArrayHasKey('success', $response, 'Response does not have success key');
            PHPUnit::assertTrue($response['success'], 'Response success is not true');
            
            if ($data !== null) {
                PHPUnit::assertArrayHasKey('data', $response, 'Response does not have data key');
                PHPUnit::assertEquals($data, $response['data']);
            }
            
            return $this;
        });
        
        // Register validation errors assertion
        TestResponse::macro('assertValidationErrors', function ($keys) {
            /** @var \Illuminate\Testing\TestResponse $this */
            $this->assertStatus(422);
            
            $response = $this->json();
            
            PHPUnit::assertArrayHasKey('errors', $response, 'Response does not have errors key');
            
            foreach ((array) $keys as $key) {
                PHPUnit::assertArrayHasKey(
                    $key, 
                    $response['errors'], 
                    "Failed to find validation error for key: {$key}"
                );
            }
            
            return $this;
        });
    }
}

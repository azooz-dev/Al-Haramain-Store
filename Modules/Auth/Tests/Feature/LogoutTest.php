<?php

namespace Modules\Auth\Tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-AUT-014: Successful Logout
 */
class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_logout_invalidates_token(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Act
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout');

        // Assert
        $response->assertStatus(200);
        
        // Verify token is invalidated
        $response2 = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/user');
        
        $response2->assertStatus(401);
    }
}


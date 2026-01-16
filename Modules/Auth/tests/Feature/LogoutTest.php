<?php

namespace Modules\Auth\tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\User;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * TC-AUT-014: Successful Logout
 */
class LogoutTest extends TestCase
{
    public function test_successful_logout_invalidates_token(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        // Verify token exists before logout
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
        ]);

        // Act - Logout
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout');

        // Assert
        $response->assertStatus(200);
        
        // Verify token is deleted from database
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
        ]);
        
        // Create a fresh request instance to verify token is invalidated
        // The previous request might have cached the user, so we need a new request
        $this->refreshApplication();
        
        // Verify token is invalidated - accessing protected route should fail
        $response2 = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/user');
        
        $response2->assertStatus(401);
    }
}


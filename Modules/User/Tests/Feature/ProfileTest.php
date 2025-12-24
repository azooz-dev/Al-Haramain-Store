<?php

namespace Modules\User\Tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-USR-001: View Own Profile
 * TC-USR-002: Update Own Profile
 * TC-USR-003: Update Other User's Profile - Denied
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_profile(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();

        // Act - The /api/user route is handled by Auth module
        $response = $this->actingAs($user, 'web')
            ->getJson('/api/user');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'first_name',
                'last_name',
                'email',
            ],
        ]);
    }

    public function test_user_can_update_own_profile(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();

        $data = [
            'first_name' => 'Updated Name',
        ];

        // Act
        $response = $this->actingAs($user, 'web')
            ->putJson("/api/users/{$user->id}", $data);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Updated Name',
        ]);
    }

    public function test_user_cannot_update_other_users_profile(): void
    {
        // Arrange
        $user1 = User::factory()->verified()->create();
        $user2 = User::factory()->verified()->create();

        $data = [
            'first_name' => 'Hacked Name',
        ];

        // Act
        $response = $this->actingAs($user1, 'web')
            ->putJson("/api/users/{$user2->id}", $data);

        // Assert
        $response->assertStatus(403);
    }
}

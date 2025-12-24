<?php

namespace Modules\Favorite\Tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\User;
use Modules\Favorite\Entities\Favorite\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-FAV-003: Add Favorite - Invalid Variant
 * TC-FAV-005: Remove Favorite
 */
class RemoveFavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_remove_favorite(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $favorite = Favorite::factory()->create(['user_id' => $user->id]);

        // Act
        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/users/{$user->id}/favorites/{$favorite->id}");

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseMissing('favorites', ['id' => $favorite->id]);
    }

    public function test_user_cannot_remove_other_users_favorite(): void
    {
        // Arrange
        $user1 = User::factory()->verified()->create();
        $user2 = User::factory()->verified()->create();
        $favorite = Favorite::factory()->create(['user_id' => $user1->id]);

        // Act - User2 trying to delete User1's favorite
        $response = $this->actingAs($user2, 'sanctum')
            ->deleteJson("/api/users/{$user1->id}/favorites/{$favorite->id}");

        // Assert
        $response->assertStatus(403);
        $this->assertDatabaseHas('favorites', ['id' => $favorite->id]);
    }
}


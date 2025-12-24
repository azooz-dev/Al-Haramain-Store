<?php

namespace Modules\Favorite\Tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\User;
use Modules\Favorite\Entities\Favorite\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-FAV-002: Add Favorite - Invalid Color
 * TC-FAV-004: List Own Favorites
 * TC-FAV-006: Access Other User's Favorites - Denied
 */
class ListFavoritesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_own_favorites(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        Favorite::factory()->count(3)->create(['user_id' => $user->id]);

        // Act
        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/users/{$user->id}/favorites");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_user_cannot_access_other_users_favorites(): void
    {
        // Arrange
        $user1 = User::factory()->verified()->create();
        $user2 = User::factory()->verified()->create();
        Favorite::factory()->count(2)->create(['user_id' => $user1->id]);

        // Act - User2 trying to access User1's favorites
        $response = $this->actingAs($user2, 'sanctum')
            ->getJson("/api/users/{$user1->id}/favorites");

        // Assert
        $response->assertStatus(403);
    }
}


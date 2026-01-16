<?php

namespace Modules\Review\tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\User;
use Modules\Order\Entities\Order\Order;
use Modules\Order\Entities\OrderItem\OrderItem;
use Modules\Review\Entities\Review\Review;
use Modules\Review\Enums\ReviewStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-REV-001: Create Review for Purchased Item
 * TC-REV-002: Create Review - Duplicate Prevented
 * TC-REV-003: Create Review - Invalid Rating
 * TC-REV-004: Create Review - Non-Owner Denied
 */
class CreateReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_review_for_purchased_item(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'delivered']);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'is_reviewed' => false,
        ]);

        $data = [
            'rating' => 5,
            'comment' => 'Great product!',
            'locale' => 'en',
        ];

        // Act
        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/users/{$user->id}/orders/{$order->id}/items/{$orderItem->id}/reviews", $data);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'rating' => 5,
            'status' => ReviewStatus::PENDING->value,
        ]);
    }

    public function test_prevents_duplicate_review_for_same_item(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'delivered']);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'is_reviewed' => true, // Already reviewed
        ]);

        $data = [
            'rating' => 5,
            'comment' => 'Another review',
            'locale' => 'en',
        ];

        // Act
        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/users/{$user->id}/orders/{$order->id}/items/{$orderItem->id}/reviews", $data);

        // Assert
        $response->assertStatus(409);
    }

    public function test_validates_rating_range(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'delivered']);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'is_reviewed' => false,
        ]);

        $data = [
            'rating' => 6, // Invalid: should be 1-5
            'comment' => 'Test comment',
            'locale' => 'en',
        ];

        // Act
        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/users/{$user->id}/orders/{$order->id}/items/{$orderItem->id}/reviews", $data);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['rating']);
    }

    public function test_prevents_non_owner_from_creating_review(): void
    {
        // Arrange
        $user1 = User::factory()->verified()->create();
        $user2 = User::factory()->verified()->create();
        $order = Order::factory()->create(['user_id' => $user1->id, 'status' => 'delivered']);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'is_reviewed' => false,
        ]);

        $data = [
            'rating' => 5,
            'comment' => 'Hacked review',
            'locale' => 'en',
        ];

        // Act - User2 trying to create review for User1's order
        $response = $this->actingAs($user2, 'sanctum')
            ->postJson("/api/users/{$user1->id}/orders/{$order->id}/items/{$orderItem->id}/reviews", $data);

        // Assert
        $response->assertStatus(403);
    }
}


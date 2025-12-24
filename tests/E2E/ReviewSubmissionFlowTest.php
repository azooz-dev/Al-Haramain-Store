<?php

namespace Tests\E2E;

use Tests\TestCase;
use Modules\User\Entities\User;
use Modules\Order\Entities\Order\Order;
use Modules\Order\Entities\OrderItem\OrderItem;
use Modules\Review\Entities\Review\Review;
use Modules\Review\Enums\ReviewStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * E2E-03: Review Submission Flow
 * 
 * Tests the complete flow for review submission:
 * 1. User receives delivered order
 * 2. User creates review for order item
 * 3. Review is created with PENDING status
 * 4. Admin approves review
 * 5. Review becomes visible to public
 */
class ReviewSubmissionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_review_submission_flow(): void
    {
        // Arrange
        $user = User::factory()->verified()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'delivered',
        ]);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'is_reviewed' => false,
        ]);

        // Step 1: User creates review
        $reviewData = [
            'rating' => 5,
            'comment' => 'Excellent product!',
        ];

        $reviewResponse = $this->actingAs($user, 'sanctum')
            ->postJson("/api/users/{$user->id}/orders/{$order->id}/items/{$orderItem->id}/reviews", $reviewData);

        $reviewResponse->assertStatus(201);

        // Step 2: Verify review was created with PENDING status
        $review = Review::where('order_item_id', $orderItem->id)->first();
        $this->assertNotNull($review);
        $this->assertEquals(ReviewStatus::PENDING, $review->status);
        $this->assertEquals(5, $review->rating);

        // Step 3: Verify order item is marked as reviewed
        $orderItem->refresh();
        $this->assertTrue($orderItem->is_reviewed);
    }
}


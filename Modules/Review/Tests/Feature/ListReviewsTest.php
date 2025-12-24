<?php

namespace Modules\Review\Tests\Feature;

use Tests\TestCase;
use Modules\Review\Entities\Review\Review;
use Modules\Review\Enums\ReviewStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-REV-007: List Reviews - Only Approved Visible
 */
class ListReviewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_approved_reviews_are_visible_to_public(): void
    {
        // Arrange
        Review::factory()->create(['status' => ReviewStatus::APPROVED]);
        Review::factory()->create(['status' => ReviewStatus::APPROVED]);
        Review::factory()->create(['status' => ReviewStatus::PENDING]);
        Review::factory()->create(['status' => ReviewStatus::REJECTED]);

        // Act
        $response = $this->getJson('/api/reviews');

        // Assert
        $response->assertStatus(200);
        $reviews = $response->json('data');
        $this->assertCount(2, $reviews);
        
        // Verify all returned reviews are approved
        foreach ($reviews as $review) {
            $this->assertEquals(ReviewStatus::APPROVED->value, $review['status']);
        }
    }
}


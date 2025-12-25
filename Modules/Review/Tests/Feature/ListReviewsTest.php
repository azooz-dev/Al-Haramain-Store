<?php

namespace Modules\Review\Tests\Feature;

use Tests\TestCase;
use Modules\Review\Entities\Review\Review;
use Modules\Review\Enums\ReviewStatus;

class ListReviewsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_only_approved_reviews_are_visible_to_public(): void
    {
        // Arrange - Create test reviews
        $approvedReview1 = Review::factory()->create(['status' => ReviewStatus::APPROVED]);
        $approvedReview2 = Review::factory()->create(['status' => ReviewStatus::APPROVED]);
        Review::factory()->create(['status' => ReviewStatus::PENDING]);
        Review::factory()->create(['status' => ReviewStatus::REJECTED]);

        // Act
        $response = $this->getJson('/api/reviews');

        // Assert
        $response->assertStatus(200);
        $reviews = $response->json('data');

        // Debug: Check what status values we're getting
        if (empty($reviews)) {
            $this->fail('No reviews returned from API');
        }

        // The status might be returned as enum object or string, so normalize it
        $approvedReviews = collect($reviews)->filter(function ($review) {
            // Handle both enum object and string status
            $status = $review['status'] ?? null;
            if ($status instanceof \BackedEnum) {
                $status = $status->value;
            }
            return $status === ReviewStatus::APPROVED->value || $status === 'approved';
        })->values()->toArray();

        // Verify we got at least our 2 approved reviews
        $this->assertGreaterThanOrEqual(
            2,
            count($approvedReviews),
            'Expected at least 2 approved reviews, but got: ' . count($approvedReviews) . ' out of ' . count($reviews) . ' total reviews. First review status: ' . json_encode($reviews[0]['status'] ?? 'null')
        );

        // Verify all returned reviews are approved (API should filter correctly)
        foreach ($reviews as $review) {
            $status = $review['status'] ?? null;
            if ($status instanceof \BackedEnum) {
                $status = $status->value;
            }
            $this->assertTrue(
                $status === ReviewStatus::APPROVED->value || $status === 'approved',
                'API should only return approved reviews. Got status: ' . json_encode($review['status'] ?? 'null')
            );
        }

        // Verify our specific reviews are in the response
        $reviewIds = collect($reviews)->pluck('identifier')->toArray();
        $this->assertContains($approvedReview1->id, $reviewIds, 'First approved review should be in response');
        $this->assertContains($approvedReview2->id, $reviewIds, 'Second approved review should be in response');
    }
}

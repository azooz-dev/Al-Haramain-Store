<?php

namespace Modules\Review\Tests\Feature;

use Tests\TestCase;
use Modules\Admin\Entities\Admin;
use Modules\Review\Entities\Review\Review;
use Modules\Review\Enums\ReviewStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * TC-REV-005: Admin Approves Review
 * TC-REV-006: Admin Rejects Review
 */
class ReviewModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_review(): void
    {
        // Arrange
        $admin = Admin::factory()->create();
        $review = Review::factory()->create(['status' => ReviewStatus::PENDING]);

        $data = [
            'status' => ReviewStatus::APPROVED->value,
        ];

        // Act
        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/reviews/{$review->id}", $data);

        // Assert
        $response->assertStatus(200);
        $review->refresh();
        $this->assertEquals(ReviewStatus::APPROVED, $review->status);
    }

    public function test_admin_can_reject_review(): void
    {
        // Arrange
        $admin = Admin::factory()->create();
        $review = Review::factory()->create(['status' => ReviewStatus::PENDING]);

        $data = [
            'status' => ReviewStatus::REJECTED->value,
        ];

        // Act
        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/reviews/{$review->id}", $data);

        // Assert
        $response->assertStatus(200);
        $review->refresh();
        $this->assertEquals(ReviewStatus::REJECTED, $review->status);
    }
}


<?php

namespace Modules\Review\tests\Unit\Services;

use Tests\TestCase;
use Modules\Review\Services\Review\ReviewService;
use Modules\Review\Repositories\Interface\Review\ReviewRepositoryInterface;
use Modules\Review\Entities\Review\Review;
use Modules\Review\Enums\ReviewStatus;
use Modules\Catalog\Contracts\ProductTranslationServiceInterface;
use Modules\Offer\Contracts\OfferServiceInterface;
use Mockery;

/**
 * TC-REV-001: Create Review for Purchased Item
 * TC-REV-002: Create Review - Duplicate Prevented
 * TC-REV-003: Create Review - Invalid Rating
 * TC-REV-005: Admin Approves Review
 * TC-REV-006: Admin Rejects Review
 */
class ReviewServiceTest extends TestCase
{
    private ReviewService $service;
    private $reviewRepositoryMock;
    private $productTranslationServiceMock;
    private $offerServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->reviewRepositoryMock = Mockery::mock(ReviewRepositoryInterface::class);
        $this->productTranslationServiceMock = Mockery::mock(ProductTranslationServiceInterface::class);
        $this->offerServiceMock = Mockery::mock(OfferServiceInterface::class);
        
        $this->service = new ReviewService(
            $this->reviewRepositoryMock,
            $this->productTranslationServiceMock,
            $this->offerServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_approves_review_successfully(): void
    {
        // Arrange
        $reviewId = 1;
        $review = Review::factory()->make(['id' => $reviewId, 'status' => ReviewStatus::PENDING]);
        $approvedReview = Review::factory()->make(['id' => $reviewId, 'status' => ReviewStatus::APPROVED]);

        $this->reviewRepositoryMock
            ->shouldReceive('updateStatus')
            ->with($reviewId, ReviewStatus::APPROVED->value)
            ->once()
            ->andReturn($approvedReview);

        // Act
        $result = $this->service->approveReview($reviewId);

        // Assert
        $this->assertInstanceOf(Review::class, $result);
        $this->assertEquals(ReviewStatus::APPROVED, $result->status);
    }

    public function test_rejects_review_successfully(): void
    {
        // Arrange
        $reviewId = 1;
        $review = Review::factory()->make(['id' => $reviewId, 'status' => ReviewStatus::PENDING]);
        $rejectedReview = Review::factory()->make(['id' => $reviewId, 'status' => ReviewStatus::REJECTED]);

        $this->reviewRepositoryMock
            ->shouldReceive('updateStatus')
            ->with($reviewId, ReviewStatus::REJECTED->value)
            ->once()
            ->andReturn($rejectedReview);

        // Act
        $result = $this->service->rejectReview($reviewId);

        // Assert
        $this->assertInstanceOf(Review::class, $result);
        $this->assertEquals(ReviewStatus::REJECTED, $result->status);
    }

    public function test_updates_review_status_successfully(): void
    {
        // Arrange
        $reviewId = 1;
        $status = ReviewStatus::APPROVED->value;
        $review = Review::factory()->make(['id' => $reviewId, 'status' => ReviewStatus::APPROVED]);

        $this->reviewRepositoryMock
            ->shouldReceive('updateStatus')
            ->with($reviewId, $status)
            ->once()
            ->andReturn($review);

        // Act
        $result = $this->service->updateReviewStatus($reviewId, $status);

        // Assert
        $this->assertInstanceOf(Review::class, $result);
    }

    public function test_throws_exception_for_invalid_status(): void
    {
        // Arrange
        $reviewId = 1;
        $invalidStatus = 'INVALID_STATUS';

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid status: {$invalidStatus}");

        $this->service->updateReviewStatus($reviewId, $invalidStatus);
    }

    public function test_gets_reviews_count_by_status(): void
    {
        // Arrange
        $status = ReviewStatus::APPROVED->value;
        $count = 5;

        $this->reviewRepositoryMock
            ->shouldReceive('countByStatus')
            ->with($status)
            ->once()
            ->andReturn($count);

        // Act
        $result = $this->service->getReviewsCountByStatus($status);

        // Assert
        $this->assertEquals($count, $result);
    }

    public function test_gets_all_reviews(): void
    {
        // Arrange
        $reviews = Review::factory()->count(3)->make();

        $this->reviewRepositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($reviews);

        // Act
        $result = $this->service->getAllReviews();

        // Assert
        $this->assertCount(3, $result);
    }
}


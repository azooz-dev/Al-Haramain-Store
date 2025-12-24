<?php

namespace Modules\Review\Contracts;

use Modules\Review\Entities\Review\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface ReviewServiceInterface
{
    /**
     * Get all reviews
     */
    public function getAllReviews(): Collection;

    /**
     * Get review by ID
     */
    public function getReviewById(int $id): Review;

    /**
     * Update a review
     */
    public function updateReview(int $id, array $data): Review;

    /**
     * Delete a review
     */
    public function deleteReview(int $id): bool;

    /**
     * Update review status
     */
    public function updateReviewStatus(int $id, string $status): Review;

    /**
     * Approve a review
     */
    public function approveReview(int $id): Review;

    /**
     * Reject a review
     */
    public function rejectReview(int $id): Review;

    /**
     * Bulk approve reviews
     */
    public function bulkApproveReviews(array $ids): int;

    /**
     * Bulk reject reviews
     */
    public function bulkRejectReviews(array $ids): int;

    /**
     * Get total reviews count
     */
    public function getReviewsCount(): int;

    /**
     * Get reviews count by status
     */
    public function getReviewsCountByStatus(string $status): int;

    /**
     * Get query builder for custom queries
     */
    public function getQueryBuilder(): Builder;

    /**
     * Get translated orderable name for a review
     */
    public function getTranslatedOrderableName(Review $review): string;

    /**
     * Get orderable identifier for a review
     */
    public function getOrderableIdentifier(Review $review): string;

    /**
     * Check if status update is allowed
     */
    public function canUpdateStatus(Review $review, string $newStatus): bool;

    /**
     * Get available statuses for a review
     */
    public function getAvailableStatuses(Review $review): array;
}



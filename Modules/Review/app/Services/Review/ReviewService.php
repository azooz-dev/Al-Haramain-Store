<?php

namespace Modules\Review\Services\Review;

use Modules\Review\Entities\Review\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Review\Repositories\Interface\Review\ReviewRepositoryInterface;
use Modules\Catalog\Services\Product\ProductTranslationService;
use Modules\Offer\Services\Offer\OfferService;

class ReviewService
{
  public function __construct(
    private ReviewRepositoryInterface $reviewRepository,
    private ProductTranslationService $productTranslationService,
    private OfferService $offerService
  ) {}

  public function getAllReviews(): Collection
  {
    return $this->reviewRepository->getAll();
  }

  public function getReviewById(int $id): Review
  {
    return $this->reviewRepository->findById($id);
  }

  public function updateReview(int $id, array $data): Review
  {
    return $this->reviewRepository->update($id, $data);
  }

  public function deleteReview(int $id): bool
  {
    return $this->reviewRepository->delete($id);
  }

  public function updateReviewStatus(int $id, string $status): Review
  {
    if (!$this->isValidStatus($status)) {
      throw new \InvalidArgumentException("Invalid status: {$status}");
    }

    return $this->reviewRepository->updateStatus($id, $status);
  }

  public function approveReview(int $id): Review
  {
    return $this->updateReviewStatus($id, Review::APPROVED);
  }

  public function rejectReview(int $id): Review
  {
    return $this->updateReviewStatus($id, Review::REJECTED);
  }

  public function bulkApproveReviews(array $ids): int
  {
    return $this->reviewRepository->bulkUpdateStatus($ids, Review::APPROVED);
  }

  public function bulkRejectReviews(array $ids): int
  {
    return $this->reviewRepository->bulkUpdateStatus($ids, Review::REJECTED);
  }

  public function getReviewsCount(): int
  {
    return $this->reviewRepository->count();
  }

  public function getReviewsCountByStatus(string $status): int
  {
    return $this->reviewRepository->countByStatus($status);
  }

  public function getQueryBuilder(): Builder
  {
    return $this->reviewRepository->getQueryBuilder();
  }

  public function getTranslatedOrderableName(Review $review): string
  {
    if (!$review->orderItem || !$review->orderItem->orderable) {
      return 'N/A';
    }

    $orderable = $review->orderItem->orderable;

    // Handle Product
    if ($orderable instanceof \Modules\Catalog\Entities\Product\Product) {
      return $this->productTranslationService->getTranslatedName($orderable);
    }

    // Handle Offer
    if ($orderable instanceof \Modules\Offer\Entities\Offer\Offer) {
      return $this->offerService->getTranslatedName($orderable);
    }

    return 'N/A';
  }

  public function getOrderableIdentifier(Review $review): string
  {
    if (!$review->orderItem || !$review->orderItem->orderable) {
      return 'N/A';
    }

    $orderable = $review->orderItem->orderable;

    // Handle Product - return SKU
    if ($orderable instanceof \Modules\Catalog\Entities\Product\Product) {
      return $orderable->sku ?? 'N/A';
    }

    // Handle Offer - return translated name or "Offer #ID" as fallback
    if ($orderable instanceof \Modules\Offer\Entities\Offer\Offer) {
      $name = $this->offerService->getTranslatedName($orderable);
      return $name ?: "Offer #{$orderable->id}";
    }

    return 'N/A';
  }

  public function canUpdateStatus(Review $review, string $newStatus): bool
  {
    if (!$this->isValidStatus($newStatus)) {
      return false;
    }

    // All status transitions are allowed for now
    // Can be enhanced with specific business rules if needed
    return true;
  }

  public function getAvailableStatuses(Review $review): array
  {
    $allStatuses = [
      Review::PENDING => __('app.status.pending'),
      Review::APPROVED => __('app.status.approved'),
      Review::REJECTED => __('app.status.rejected'),
    ];

    // Filter out invalid statuses based on business rules
    $available = [];
    foreach ($allStatuses as $status => $label) {
      if ($this->canUpdateStatus($review, $status)) {
        $available[$status] = $label;
      }
    }

    return $available;
  }

  private function isValidStatus(string $status): bool
  {
    return in_array($status, [
      Review::PENDING,
      Review::APPROVED,
      Review::REJECTED,
    ]);
  }
}

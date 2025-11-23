<?php

namespace App\Repositories\Interface\Review;

use App\Models\Review\Review;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface ReviewRepositoryInterface
{
  public function getAll(): Collection;

  public function findById(int $id): Review;

  public function update(int $id, array $data): Review;

  public function delete(int $id): bool;

  public function count(): int;

  public function countByStatus(string $status): int;

  public function getQueryBuilder(): Builder;

  public function updateStatus(int $id, string $status): Review;

  public function bulkUpdateStatus(array $ids, string $status): int;

  // Widget-specific methods
  public function getReviewsCountByDateRange(Carbon $start, Carbon $end): int;

  public function getAverageRating(Carbon $start, Carbon $end): float;

  public function getRatingDistribution(Carbon $start, Carbon $end): Collection;
}

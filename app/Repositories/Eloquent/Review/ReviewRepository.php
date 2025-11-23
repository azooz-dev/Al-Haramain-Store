<?php

namespace App\Repositories\Eloquent\Review;

use App\Models\Review\Review;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Repositories\Interface\Review\ReviewRepositoryInterface;

class ReviewRepository implements ReviewRepositoryInterface
{
  public function getAll(): Collection
  {
    return Review::with([
      'user',
      'order',
      'orderItem.orderable' => function ($morphTo) {
        $morphTo->morphWith([
          \App\Models\Product\Product::class => ['translations'],
          \App\Models\Offer\Offer::class => ['translations'],
        ]);
      },
    ])->get();
  }

  public function findById(int $id): Review
  {
    return Review::with([
      'user',
      'order',
      'orderItem.orderable' => function ($morphTo) {
        $morphTo->morphWith([
          \App\Models\Product\Product::class => ['translations'],
          \App\Models\Offer\Offer::class => ['translations'],
        ]);
      },
    ])->findOrFail($id);
  }

  public function update(int $id, array $data): Review
  {
    $review = $this->findById($id);
    $review->update($data);
    $review->refresh();

    return $review->load([
      'user',
      'order',
      'orderItem.orderable' => function ($morphTo) {
        $morphTo->morphWith([
          \App\Models\Product\Product::class => ['translations'],
          \App\Models\Offer\Offer::class => ['translations'],
        ]);
      },
    ]);
  }

  public function delete(int $id): bool
  {
    $review = $this->findById($id);
    return $review->delete();
  }

  public function count(): int
  {
    return Review::count();
  }

  public function countByStatus(string $status): int
  {
    return Review::where('status', $status)->count();
  }

  public function getQueryBuilder(): Builder
  {
    return Review::query()
      ->with([
        'user',
        'order',
        'orderItem.orderable' => function ($morphTo) {
          $morphTo->morphWith([
            \App\Models\Product\Product::class => ['translations'],
            \App\Models\Offer\Offer::class => ['translations'],
          ]);
        },
      ])
      ->orderBy('created_at', 'desc');
  }

  public function updateStatus(int $id, string $status): Review
  {
    return $this->update($id, ['status' => $status]);
  }

  public function bulkUpdateStatus(array $ids, string $status): int
  {
    return Review::whereIn('id', $ids)->update(['status' => $status]);
  }

  // Widget-specific methods
  public function getReviewsCountByDateRange(Carbon $start, Carbon $end): int
  {
    return Review::whereBetween('created_at', [$start, $end])->count();
  }

  public function getAverageRating(Carbon $start, Carbon $end): float
  {
    return Review::whereBetween('created_at', [$start, $end])
      ->where('status', Review::APPROVED)
      ->avg('rating') ?? 0;
  }

  public function getRatingDistribution(Carbon $start, Carbon $end): Collection
  {
    return Review::whereBetween('created_at', [$start, $end])
      ->select('rating', DB::raw('COUNT(*) as count'))
      ->groupBy('rating')
      ->orderBy('rating')
      ->get();
  }
}

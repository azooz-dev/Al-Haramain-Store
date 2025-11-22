<?php

namespace App\Repositories\Eloquent\Favorite;

use App\Models\Favorite\Favorite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use App\Repositories\Interface\Favorite\FavoriteRepositoryInterface;

class FavoriteRepository implements FavoriteRepositoryInterface
{
  public function getAll(): Collection
  {
    return Favorite::with([
      'user',
      'product.translations',
      'productColor',
      'productVariant',
    ])->get();
  }

  public function findById(int $id): Favorite
  {
    return Favorite::with([
      'user',
      'product.translations',
      'productColor',
      'productVariant',
    ])->findOrFail($id);
  }

  public function count(): int
  {
    return Favorite::count();
  }

  public function countRecent(int $days = 7): int
  {
    return Favorite::where('created_at', '>=', now()->subDays($days))->count();
  }

  public function getQueryBuilder(): Builder
  {
    return Favorite::query()
      ->with([
        'user',
        'product.translations',
        'productColor',
        'productVariant',
      ]);
  }
}

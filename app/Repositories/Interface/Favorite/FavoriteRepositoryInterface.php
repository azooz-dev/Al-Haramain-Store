<?php

namespace App\Repositories\Interface\Favorite;

use App\Models\Favorite\Favorite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface FavoriteRepositoryInterface
{
  public function getAll(): Collection;

  public function findById(int $id): Favorite;

  public function count(): int;

  public function countRecent(int $days = 7): int;

  public function getQueryBuilder(): Builder;
}

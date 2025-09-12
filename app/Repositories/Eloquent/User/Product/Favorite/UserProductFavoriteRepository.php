<?php

namespace App\Repositories\Eloquent\User\Product\Favorite;

use App\Models\Favorite\Favorite;
use App\Repositories\Interface\User\Product\Favorite\UserProductFavoriteRepositoryInterface;

class UserProductFavoriteRepository implements UserProductFavoriteRepositoryInterface
{
  public function store(array $data): bool
  {
    $favorite = Favorite::create($data);

    return $favorite->wasRecentlyCreated;
  }
}

<?php

namespace App\Repositories\Interface\User\Product\Favorite;

use App\Models\Favorite\Favorite;

interface UserProductFavoriteRepositoryInterface
{
  public function store(array $data): Favorite;
}

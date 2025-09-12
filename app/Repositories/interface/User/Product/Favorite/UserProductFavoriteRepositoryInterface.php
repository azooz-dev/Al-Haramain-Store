<?php

namespace App\Repositories\Interface\User\Product\Favorite;

interface UserProductFavoriteRepositoryInterface
{
  public function store(array $data): bool;
}

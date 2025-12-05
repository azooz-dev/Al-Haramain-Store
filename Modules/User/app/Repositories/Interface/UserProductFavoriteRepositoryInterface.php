<?php

namespace Modules\User\Repositories\Interface;

use App\Models\Favorite\Favorite;

interface UserProductFavoriteRepositoryInterface
{
  public function store(array $data): Favorite;
}

<?php

namespace Modules\User\Repositories\Interface;

use Modules\Favorite\Entities\Favorite\Favorite;

interface UserProductFavoriteRepositoryInterface
{
  public function store(array $data): Favorite;
}

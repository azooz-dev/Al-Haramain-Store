<?php

namespace Modules\User\Repositories\Eloquent;

use Modules\Favorite\Entities\Favorite\Favorite;
use Modules\User\Repositories\Interface\UserProductFavoriteRepositoryInterface;

class UserProductFavoriteRepository implements UserProductFavoriteRepositoryInterface
{
  public function store(array $data): Favorite
  {
    $favorite = Favorite::create($data);

    return $favorite;
  }
}

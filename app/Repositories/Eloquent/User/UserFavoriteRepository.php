<?php

namespace App\Repositories\Eloquent\User;

use App\Models\Favorite\Favorite;
use App\Repositories\Interface\User\UserFavoriteRepositoryInterface;

class UserFavoriteRepository implements UserFavoriteRepositoryInterface
{
  public function getAllUserFavorites(int $userId)
  {
    return Favorite::where('user_id', $userId)->get();
  }

  public function deleteFavorite(array $data): bool
  {
    return Favorite::where($data)->delete();
  }
}

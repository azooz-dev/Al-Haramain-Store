<?php

namespace Modules\User\Repositories\Eloquent;

use App\Models\Favorite\Favorite;
use Modules\User\Repositories\Interface\UserFavoriteRepositoryInterface;

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

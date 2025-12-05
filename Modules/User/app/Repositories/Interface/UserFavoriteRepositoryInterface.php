<?php

namespace Modules\User\Repositories\Interface;

interface UserFavoriteRepositoryInterface
{
  public function getAllUserFavorites(int $userId);

  public function deleteFavorite(array $data): bool;
}

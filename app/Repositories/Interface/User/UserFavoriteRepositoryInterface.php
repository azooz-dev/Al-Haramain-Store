<?php

namespace App\Repositories\Interface\User;

interface UserFavoriteRepositoryInterface
{
  public function getAllUserFavorites(int $userId);

  public function deleteFavorite(array $data): bool;
}

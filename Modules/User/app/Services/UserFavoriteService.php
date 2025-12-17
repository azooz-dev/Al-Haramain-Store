<?php

namespace Modules\User\Services;

use Modules\Favorite\Exceptions\Favorite\FavoriteException;
use Modules\Favorite\Http\Resources\Favorite\FavoriteApiResource;
use Modules\User\Repositories\Interface\UserFavoriteRepositoryInterface;

use function App\Helpers\errorResponse;
use function App\Helpers\showMessage;

class UserFavoriteService
{
  public function __construct(private UserFavoriteRepositoryInterface $userFavoriteRepository) {}

  public function getAllUserFavorites(int $userId)
  {
    try {
      $userFavorites = $this->userFavoriteRepository->getAllUserFavorites($userId);

      if (!$userFavorites) {
        return errorResponse(__("app.messages.favorite.favorite_not_found"), 404);
      }

      return FavoriteApiResource::collection($userFavorites);
    } catch (FavoriteException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }

  public function deleteFavorite(array $data)
  {
    try {
      $deletedFavorite = $this->userFavoriteRepository->deleteFavorite($data);

      if (!$deletedFavorite) {
        return errorResponse(__("app.messages.favorite.favorite_not_found"), 404);
      }

      return showMessage(__("app.messages.favorite.favorite_deleted"), 200);
    } catch (FavoriteException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }
}

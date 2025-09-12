<?php

namespace App\Services\User\Product\Favorite;

use App\Exceptions\Favorite\FavoriteException;
use App\Repositories\Interface\User\Product\Favorite\UserProductFavoriteRepositoryInterface;

use function App\Helpers\errorResponse;
use function App\Helpers\showMessage;

class UserProductFavoriteService
{
  public function __construct(private UserProductFavoriteRepositoryInterface $userProductFavorite) {}

  public function storeFavorite(array $data)
  {
    try {
      $favorite = $this->userProductFavorite->store($data);

      if (!$favorite) {
        return errorResponse(__("app.messages.favorite.favorite_not_found"), 404);
      }

      return showMessage(__("app.messages.favorite.favorite_created"), 201);
    } catch (FavoriteException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }
}

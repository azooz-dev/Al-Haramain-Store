<?php

namespace Modules\User\Services;

use Modules\Favorite\Exceptions\Favorite\FavoriteException;
use Modules\Favorite\Http\Resources\Favorite\FavoriteApiResource;
use Modules\User\Repositories\Interface\UserProductFavoriteRepositoryInterface;
use function App\Helpers\errorResponse;

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

      return new FavoriteApiResource($favorite);
    } catch (FavoriteException $e) {
      return errorResponse($e->getMessage(), $e->getCode());
    }
  }
}

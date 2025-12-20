<?php

namespace Modules\Favorite\Services\Favorite;

use Modules\Favorite\Entities\Favorite\Favorite;
use Modules\Favorite\Contracts\FavoriteServiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Favorite\Repositories\Interface\Favorite\FavoriteRepositoryInterface;
use Modules\Catalog\Contracts\ProductTranslationServiceInterface;

class FavoriteService implements FavoriteServiceInterface
{
  public function __construct(
    private FavoriteRepositoryInterface $favoriteRepository,
    private ProductTranslationServiceInterface $productTranslationService
  ) {}

  public function getAllFavorites(): Collection
  {
    return $this->favoriteRepository->getAll();
  }

  public function getFavoriteById(int $id): Favorite
  {
    return $this->favoriteRepository->findById($id);
  }

  public function getFavoritesCount(): int
  {
    return $this->favoriteRepository->count();
  }

  public function getRecentFavoritesCount(int $days = 7): int
  {
    return $this->favoriteRepository->countRecent($days);
  }

  public function getQueryBuilder(): Builder
  {
    return $this->favoriteRepository->getQueryBuilder();
  }

  public function getTranslatedProductName(Favorite $favorite): string
  {
    if (!$favorite->product) {
      return 'N/A';
    }

    return $this->productTranslationService->getTranslatedName($favorite->product);
  }
}

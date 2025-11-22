<?php

namespace App\Services\Favorite;

use App\Models\Favorite\Favorite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use App\Repositories\Interface\Favorite\FavoriteRepositoryInterface;
use App\Services\Product\ProductTranslationService;

class FavoriteService
{
  public function __construct(
    private FavoriteRepositoryInterface $favoriteRepository,
    private ProductTranslationService $productTranslationService
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

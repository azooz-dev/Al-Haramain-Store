<?php

namespace Modules\Favorite\Contracts;

use Modules\Favorite\Entities\Favorite\Favorite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface FavoriteServiceInterface
{
    /**
     * Get all favorites
     */
    public function getAllFavorites(): Collection;

    /**
     * Get favorite by ID
     */
    public function getFavoriteById(int $id): Favorite;

    /**
     * Get total favorites count
     */
    public function getFavoritesCount(): int;

    /**
     * Get recent favorites count
     */
    public function getRecentFavoritesCount(int $days = 7): int;

    /**
     * Get query builder for custom queries
     */
    public function getQueryBuilder(): Builder;

    /**
     * Get translated product name for a favorite
     */
    public function getTranslatedProductName(Favorite $favorite): string;
}


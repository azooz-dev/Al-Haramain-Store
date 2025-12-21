<?php

namespace Modules\User\Contracts;

interface UserFavoriteServiceInterface
{
    /**
     * Get all favorites for a user
     */
    public function getAllUserFavorites(int $userId);

    /**
     * Delete a favorite
     */
    public function deleteFavorite(array $data);
}


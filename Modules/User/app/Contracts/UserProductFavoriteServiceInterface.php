<?php

namespace Modules\User\Contracts;

interface UserProductFavoriteServiceInterface
{
    /**
     * Store a product favorite for a user
     */
    public function storeFavorite(array $data);
}


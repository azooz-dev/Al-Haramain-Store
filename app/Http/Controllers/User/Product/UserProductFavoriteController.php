<?php

namespace App\Http\Controllers\User\Product;

use App\Http\Controllers\Controller;
use App\Services\User\Product\Favorite\UserProductFavoriteService;

class UserProductFavoriteController extends Controller
{
    public function __construct(private UserProductFavoriteService $userProductFavoriteService) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(int $userId, int $productId, int $colorId, int $variantId)
    {
        $response = $this->userProductFavoriteService->storeFavorite(['user_id' => $userId, "product_id" => $productId, "color_id" => $colorId, "variant_id" => $variantId]);

        return $response;
    }
}

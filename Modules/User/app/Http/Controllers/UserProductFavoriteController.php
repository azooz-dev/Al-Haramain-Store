<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\Contracts\UserProductFavoriteServiceInterface;

use function App\Helpers\showOne;

class UserProductFavoriteController extends Controller
{
    public function __construct(private UserProductFavoriteServiceInterface $userProductFavoriteService) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(int $userId, int $productId, int $colorId, int $variantId)
    {
        $favorite = $this->userProductFavoriteService->storeFavorite(['user_id' => $userId, "product_id" => $productId, "color_id" => $colorId, "variant_id" => $variantId]);

        return showOne($favorite, 'success', 201);
    }
}

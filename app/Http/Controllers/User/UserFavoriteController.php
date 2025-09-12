<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\UserFavoriteService;

use function App\Helpers\showAll;

class UserFavoriteController extends Controller
{
    public function __construct(private UserFavoriteService $userFavoriteService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(int $userId)
    {
        $userFavorites = $this->userFavoriteService->getAllUserFavorites($userId);

        return showAll($userFavorites, 'User Favorites', 200);
    }
}

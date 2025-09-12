<?php

namespace App\Http\Controllers\User;

use function App\Helpers\showAll;
use App\Http\Controllers\Controller;

use App\Services\User\UserFavoriteService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserFavoriteController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private UserFavoriteService $userFavoriteService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(int $userId)
    {
        $userFavorites = $this->userFavoriteService->getAllUserFavorites($userId);

        return showAll($userFavorites, 'User Favorites', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $userId, $favoriteId)
    {
        if ($this->authorize('delete', $favoriteId))
            $response = $this->userFavoriteService->deleteFavorite(['userId' => $userId, 'id' => $favoriteId]);

        return $response;
    }
}

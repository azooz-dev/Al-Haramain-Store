<?php

namespace Modules\User\Http\Controllers;

use function App\Helpers\showAll;
use App\Http\Controllers\Controller;
use App\Models\Favorite\Favorite;
use Modules\User\Services\UserFavoriteService;
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
    public function destroy(int $userId, Favorite $favorite)
    {
        $this->authorize('delete', $favorite);
        $response = $this->userFavoriteService->deleteFavorite(['user_id' => $userId, 'id' => $favorite->id]);

        return $response;
    }
}

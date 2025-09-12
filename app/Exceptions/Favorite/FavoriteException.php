<?php

namespace App\Exceptions\Favorite;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use function App\Helpers\errorResponse;

class FavoriteException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return errorResponse($this->getMessage() ?: __("app.messages.favorite.error_occurred!"), $this->getCode() ?: 500);
    }
}

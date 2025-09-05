<?php

namespace App\Exceptions\Order\Review;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function App\Helpers\errorResponse;

class ReviewException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return errorResponse($this->getMessage() ?: __("app.messages.review.review_error"), $this->getCode() ?: 500);
    }
}

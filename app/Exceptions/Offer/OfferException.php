<?php

namespace App\Exceptions\Offer;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function App\Helpers\errorResponse;

class OfferException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return errorResponse($this->getMessage() ?: __("app.messages.offer.offer_error"), $this->getCode() ?: 500);
    }
}

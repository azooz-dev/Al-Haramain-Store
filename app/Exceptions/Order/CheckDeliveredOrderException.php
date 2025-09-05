<?php

namespace App\Exceptions\Order;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function App\Helpers\errorResponse;

class CheckDeliveredOrderException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return errorResponse($this->getMessage() ?: __("app.messages.order.error_order_status"), $this->getCode() ?: 500);
    }
}

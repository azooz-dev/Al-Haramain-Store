<?php

namespace Modules\Order\Exceptions\Order;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use function App\Helpers\errorResponse;

class OrderException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return errorResponse($this->getMessage() ?: __('app.messages.order.order_error'), $this->getCode() ?: 500);
    }
}

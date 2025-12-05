<?php

namespace Modules\Catalog\Exceptions\Product\Variant;

use Exception;
use Illuminate\Http\JsonResponse;

use function App\Helpers\errorResponse;

class OutOfStockException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return errorResponse($this->getMessage() ?: __('app.messages.product.product_error'), $this->getCode() ?: 500);
    }
}

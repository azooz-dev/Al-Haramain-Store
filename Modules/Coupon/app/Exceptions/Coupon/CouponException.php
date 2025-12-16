<?php

namespace Modules\Coupon\Exceptions\Coupon;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use function App\Helpers\errorResponse;

class CouponException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return errorResponse($this->getMessage() ?: __("app.messages.coupon.coupon_error"), $this->getCode() ?: 500);
    }
}

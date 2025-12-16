<?php

namespace Modules\Payment\Exceptions\Payment;

use Exception;
use Illuminate\Http\JsonResponse;
use function App\Helpers\errorResponse;

class InvalidPaymentMethodException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return errorResponse($this->getMessage() ?: __('app.messages.payment.invalid_payment_method'), $this->getCode() ?: 400);
    }
}

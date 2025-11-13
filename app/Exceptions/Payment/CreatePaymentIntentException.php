<?php

namespace App\Exceptions\Payment;

use Exception;
use Illuminate\Http\JsonResponse;

use function App\Helpers\errorResponse;

class CreatePaymentIntentException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return errorResponse($this->getMessage() ?: __('app.messages.payment.create_payment_intent_error'), $this->getCode() ?: 500);
    }
}

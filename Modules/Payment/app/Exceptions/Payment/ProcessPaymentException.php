<?php

namespace Modules\Payment\Exceptions\Payment;

use Exception;
use Illuminate\Http\JsonResponse;
use function App\Helpers\errorResponse;

class ProcessPaymentException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return errorResponse($this->getMessage() ?: __("app.messages.payment.process_payment_error"), $this->getCode() ?: 500);
    }
}

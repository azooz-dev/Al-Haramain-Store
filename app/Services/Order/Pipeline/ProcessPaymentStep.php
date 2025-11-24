<?php

namespace App\Services\Order\Pipeline;

use App\Services\Payment\PaymentService;

class ProcessPaymentStep implements OrderProcessingStep
{
    public function __construct(private PaymentService $paymentService) {}

    public function handle(array $data, \Closure $next)
    {
        // Process payment
        $paymentResult = $this->paymentService->processPayment($data);
        
        // Store payment result for later
        $data['_payment_result'] = $paymentResult;

        return $next($data);
    }
}



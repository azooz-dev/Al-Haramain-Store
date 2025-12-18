<?php

namespace Modules\Order\Services\Order\Pipeline;

use Modules\Payment\Contracts\PaymentServiceInterface;

class ProcessPaymentStep implements OrderProcessingStep
{
    public function __construct(private PaymentServiceInterface $paymentService) {}

    public function handle(array $data, \Closure $next)
    {
        // Process payment
        $paymentResult = $this->paymentService->processPayment($data);
        
        // Store payment result for later
        $data['_payment_result'] = $paymentResult;

        return $next($data);
    }
}



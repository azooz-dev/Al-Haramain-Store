<?php

namespace Modules\Order\Services\Order\Pipeline;

use Modules\Payment\Contracts\PaymentServiceInterface;

class RecordPaymentStep implements OrderProcessingStep
{
    private const PAYMENT_METHOD_CREDIT_CARD = 'credit_card';

    public function __construct(
        private PaymentServiceInterface $paymentService
    ) {}

    public function handle(array $data, \Closure $next)
    {
        $order = $data['_order'] ?? null;
        $paymentResult = $data['_payment_result'] ?? null;
        $paymentMethod = $data['payment_method'] ?? null;

        if ($paymentMethod === self::PAYMENT_METHOD_CREDIT_CARD && 
            $paymentResult?->transactionId && 
            $order) {
            $this->paymentService->createPayment($order->id, $paymentResult);
        }

        return $next($data);
    }
}


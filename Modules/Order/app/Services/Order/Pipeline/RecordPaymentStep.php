<?php

namespace Modules\Order\Services\Order\Pipeline;

use Modules\Order\Entities\Order\Order;
use App\Services\Payment\PaymentService;

class RecordPaymentStep implements OrderProcessingStep
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function handle(array $data, \Closure $next)
    {
        $order = $data['_order'] ?? null;
        $paymentResult = $data['_payment_result'] ?? null;
        $paymentMethod = $data['payment_method'] ?? null;

        if ($paymentMethod === Order::PAYMENT_METHOD_CREDIT_CARD && 
            $paymentResult?->transactionId && 
            $order) {
            $this->paymentService->createPayment($order->id, $paymentResult);
        }

        return $next($data);
    }
}


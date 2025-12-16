<?php

namespace Modules\Payment\Services\Payment;

use Modules\Order\Entities\Order\Order;
use Modules\Payment\Contracts\PaymentProcessorInterface;
use Modules\Payment\Services\Payment\Processors\CashOnDeliveryProcessor;
use Modules\Payment\Services\Payment\Processors\StripePaymentProcessor;
use Modules\Payment\Exceptions\Payment\InvalidPaymentMethodException;

class PaymentProcessorFactory
{
    public function __construct(
        private CashOnDeliveryProcessor $codProcessor,
        private StripePaymentProcessor $stripeProcessor,
    ) {}

    public function make(string $paymentMethod): PaymentProcessorInterface
    {
        return match ($paymentMethod) {
            Order::PAYMENT_METHOD_CASH_ON_DELIVERY => $this->codProcessor,
            Order::PAYMENT_METHOD_CREDIT_CARD => $this->stripeProcessor,
            default => throw new InvalidPaymentMethodException(
                __('app.messages.payment.invalid_payment_method'), 
                400
            ),
        };
    }
}



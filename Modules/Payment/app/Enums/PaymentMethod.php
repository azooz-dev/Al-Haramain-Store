<?php

namespace Modules\Payment\Enums;

enum PaymentMethod: string
{
    case CASH_ON_DELIVERY = 'cash_on_delivery';
    case CREDIT_CARD = 'credit_card';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::CASH_ON_DELIVERY => __('app.payment_method.cash_on_delivery'),
            self::CREDIT_CARD => __('app.payment_method.credit_card'),
        };
    }

    /**
     * Check if payment method requires online payment
     */
    public function requiresOnlinePayment(): bool
    {
        return match ($this) {
            self::CASH_ON_DELIVERY => false,
            self::CREDIT_CARD => true,
        };
    }

    /**
     * Get icon for UI
     */
    public function icon(): string
    {
        return match ($this) {
            self::CASH_ON_DELIVERY => 'heroicon-m-banknotes',
            self::CREDIT_CARD => 'heroicon-m-credit-card',
        };
    }

    /**
     * Get all methods as options for select
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}


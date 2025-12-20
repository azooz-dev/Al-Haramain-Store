<?php

namespace Modules\Coupon\Enums;

enum CouponType: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::FIXED => __('app.coupon.type.fixed'),
            self::PERCENTAGE => __('app.coupon.type.percentage'),
        };
    }

    /**
     * Get icon for UI
     */
    public function icon(): string
    {
        return match ($this) {
            self::FIXED => 'heroicon-m-currency-dollar',
            self::PERCENTAGE => 'heroicon-m-percent-badge',
        };
    }

    /**
     * Get all types as options for select
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


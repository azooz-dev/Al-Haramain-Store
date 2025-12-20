<?php

namespace Modules\Payment\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('app.payment_status.pending'),
            self::SUCCESS => __('app.payment_status.paid'),
            self::FAILED => __('app.payment_status.failed'),
        };
    }

    /**
     * Get color for UI badges
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::SUCCESS => 'success',
            self::FAILED => 'danger',
        };
    }

    /**
     * Get icon for UI
     */
    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-m-clock',
            self::SUCCESS => 'heroicon-m-check-circle',
            self::FAILED => 'heroicon-m-x-circle',
        };
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this === self::SUCCESS;
    }

    /**
     * Get all statuses as options for select
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


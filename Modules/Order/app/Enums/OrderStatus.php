<?php

namespace Modules\Order\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('app.status.pending'),
            self::PROCESSING => __('app.status.processing'),
            self::SHIPPED => __('app.status.shipped'),
            self::DELIVERED => __('app.status.delivered'),
            self::CANCELLED => __('app.status.cancelled'),
            self::REFUNDED => __('app.status.refunded'),
        };
    }

    /**
     * Get color for UI badges
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::SHIPPED => 'primary',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
            self::REFUNDED => 'gray',
        };
    }

    /**
     * Get icon for UI
     */
    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-m-clock',
            self::PROCESSING => 'heroicon-m-cog-6-tooth',
            self::SHIPPED => 'heroicon-m-truck',
            self::DELIVERED => 'heroicon-m-check-circle',
            self::CANCELLED => 'heroicon-m-x-circle',
            self::REFUNDED => 'heroicon-m-arrow-path',
        };
    }

    /**
     * Get rgba color for charts
     */
    public function chartColor(): string
    {
        return match ($this) {
            self::PENDING => 'rgba(245, 158, 11, 0.8)',
            self::PROCESSING => 'rgba(59, 130, 246, 0.8)',
            self::SHIPPED => 'rgba(139, 92, 246, 0.8)',
            self::DELIVERED => 'rgba(34, 197, 94, 0.8)',
            self::CANCELLED => 'rgba(239, 68, 68, 0.8)',
            self::REFUNDED => 'rgba(107, 114, 128, 0.8)',
        };
    }

    /**
     * Statuses to exclude from revenue/stats calculations
     */
    public static function excludedFromStats(): array
    {
        return [self::CANCELLED, self::REFUNDED];
    }

    /**
     * Get all statuses as array for dropdowns
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get statuses with labels for select options
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    /**
     * Get available transitions from current status
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDING => [self::PROCESSING, self::SHIPPED, self::CANCELLED],
            self::PROCESSING => [self::SHIPPED, self::CANCELLED],
            self::SHIPPED => [self::DELIVERED, self::CANCELLED],
            self::DELIVERED => [self::REFUNDED],
            self::CANCELLED, self::REFUNDED => [],
        };
    }

    /**
     * Check if transition to given status is allowed
     */
    public function canTransitionTo(self $status): bool
    {
        return in_array($status, $this->allowedTransitions());
    }

    /**
     * Check if this status can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this, [self::PENDING, self::PROCESSING]);
    }

    /**
     * Check if this status can be refunded
     */
    public function canBeRefunded(): bool
    {
        return $this === self::DELIVERED;
    }

    /**
     * Check if this status can be edited
     */
    public function canBeEdited(): bool
    {
        return in_array($this, [self::PENDING, self::PROCESSING, self::SHIPPED]);
    }
}


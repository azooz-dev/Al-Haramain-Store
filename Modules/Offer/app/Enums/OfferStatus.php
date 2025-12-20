<?php

namespace Modules\Offer\Enums;

enum OfferStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('app.status.active'),
            self::INACTIVE => __('app.status.inactive'),
        };
    }

    /**
     * Get color for UI badges
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
        };
    }

    /**
     * Get icon for UI
     */
    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE => 'heroicon-m-check-circle',
            self::INACTIVE => 'heroicon-m-x-circle',
        };
    }

    /**
     * Check if the status is active
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
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


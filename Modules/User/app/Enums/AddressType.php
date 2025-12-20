<?php

namespace Modules\User\Enums;

enum AddressType: string
{
    case HOME = 'Home';
    case WORK = 'Work';
    case OTHER = 'Other';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::HOME => __('app.address.type.home'),
            self::WORK => __('app.address.type.work'),
            self::OTHER => __('app.address.type.other'),
        };
    }

    /**
     * Get icon for UI
     */
    public function icon(): string
    {
        return match ($this) {
            self::HOME => 'heroicon-m-home',
            self::WORK => 'heroicon-m-building-office',
            self::OTHER => 'heroicon-m-map-pin',
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


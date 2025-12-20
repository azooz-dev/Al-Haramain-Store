<?php

namespace Modules\Coupon\Entities\Coupon;

use Modules\Order\Entities\Order\Order;
use Modules\Coupon\Enums\CouponType;
use Modules\Coupon\Enums\CouponStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Coupon\Database\Factories\Coupon\CouponFactory;

class Coupon extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return CouponFactory::new();
    }

    /**
     * @deprecated Use CouponType::FIXED instead
     */
    const FIXED = 'fixed';
    /**
     * @deprecated Use CouponType::PERCENTAGE instead
     */
    const PERCENTAGE = 'percentage';
    /**
     * @deprecated Use CouponStatus::ACTIVE instead
     */
    const ACTIVE = 'active';
    /**
     * @deprecated Use CouponStatus::INACTIVE instead
     */
    const INACTIVE = 'inactive';

    protected $fillable = [
        'code',
        'name',
        'type',
        'discount_amount',
        'usage_limit',
        'usage_limit_per_user',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'type' => CouponType::class,
        'status' => CouponStatus::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'discount_amount' => 'decimal:2',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function couponUsers(): HasMany
    {
        return $this->hasMany(CouponUser::class);
    }

    /**
     * Get status color for badges
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status?->color() ?? 'gray';
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type?->label() ?? '';
    }
}

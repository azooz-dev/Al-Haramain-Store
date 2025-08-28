<?php

namespace App\Models\Coupon;

use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    const FIXED = 'fixed';
    const PERCENTAGE = 'percentage';
    const ACTIVE = 'active';
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

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function couponUsers(): HasMany
    {
        return $this->hasMany(CouponUser::class);
    }
}

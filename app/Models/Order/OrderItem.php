<?php

namespace App\Models\Order;

use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'quantity',
        'total_price',
        'amount_discount_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
        'amount_discount_price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the effective price per item (after discount)
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->total_price - ($this->amount_discount_price ?? 0);
    }

    /**
     * Get the original line total (before discounts)
     */
    public function getOriginalLineTotalAttribute(): float
    {
        return $this->total_price * $this->quantity;
    }

    /**
     * Get the total discount for this line item
     */
    public function getTotalDiscountAttribute(): float
    {
        return ($this->amount_discount_price ?? 0) * $this->quantity;
    }

    /**
     * Get the discount percentage for this item
     */
    public function getDiscountPercentageAttribute(): float
    {
        if (!$this->amount_discount_price || $this->total_price <= 0) {
            return 0;
        }

        return round(($this->amount_discount_price / $this->total_price) * 100, 2);
    }

    /**
     * Check if this item has a discount
     */
    public function hasDiscount(): bool
    {
        return $this->amount_discount_price && $this->amount_discount_price > 0;
    }

    /**
     * Get formatted orderable name with translations
     */
    public function getOrderableNameAttribute(): string
    {
        if (!$this->orderable) {
            return 'Orderable Not Found';
        }

        return $this->orderable->translations
            ->where('local', app()->getLocale())
            ->first()?->name ??
            $this->orderable->translations->first()?->name ?? '';
    }
}

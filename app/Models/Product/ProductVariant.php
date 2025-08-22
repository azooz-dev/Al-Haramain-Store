<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'color_id',
        'size',
        'price',
        'amount_discount_price',
        'quantity',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'color_id' => 'integer',
        'price' => 'decimal:2',
        'amount_discount_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class, 'color_id');
    }

    /**
     * Get the effective price (discounted if available)
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->amount_discount_price ?? $this->price;
    }

    /**
     * Get the discount amount
     */
    public function getDiscountAmountAttribute(): float
    {
        if (!$this->amount_discount_price || $this->amount_discount_price >= $this->price) {
            return 0;
        }

        return $this->price - $this->amount_discount_price;
    }

    /**
     * Get the discount percentage
     */
    public function getDiscountPercentageAttribute(): float
    {
        $discountAmount = $this->discount_amount;

        if ($discountAmount <= 0 || $this->price <= 0) {
            return 0;
        }

        return round(($discountAmount / $this->price) * 100, 2);
    }

    /**
     * Check if this variant has a discount
     */
    public function hasDiscount(): bool
    {
        return $this->amount_discount_price &&
            $this->amount_discount_price < $this->price;
    }

    /**
     * Check if this variant is in stock
     */
    public function inStock(): bool
    {
        return $this->quantity > 0;
    }

    /**
     * Check if this variant is low stock
     */
    public function lowStock(int $threshold = 5): bool
    {
        return $this->quantity > 0 && $this->quantity <= $threshold;
    }

    /**
     * Get formatted size-color combination
     */
    public function getDisplayNameAttribute(): string
    {
        $colorCode = $this->color?->color_code ?
            strtoupper($this->color->color_code) : 'Unknown Color';

        return "{$this->size} - {$colorCode}";
    }
}

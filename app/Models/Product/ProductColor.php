<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductColor extends Model
{
    protected $fillable = [
        'product_id',
        'color_code'
    ];

    protected $casts = [
        'product_id' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductColorImage::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'color_id');
    }

    /**
     * Get the color name in uppercase
     */
    public function getColorNameAttribute(): string
    {
        return strtoupper($this->color_code);
    }

    /**
     * Get the primary image for this color
     */
    public function getPrimaryImageAttribute(): ?ProductColorImage
    {
        return $this->images()->first();
    }

    /**
     * Check if this color has images
     */
    public function hasImages(): bool
    {
        return $this->images()->exists();
    }

    /**
     * Get the total stock for this color across all variants
     */
    public function getTotalStockAttribute(): int
    {
        return $this->variants->sum('quantity');
    }
}

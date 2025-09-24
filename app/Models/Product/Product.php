<?php

namespace App\Models\Product;

use App\Models\Offer\Offer;
use App\Models\Review\Review;
use App\Models\Order\OrderItem;
use App\Models\Category\Category;
use App\Models\Product\ProductColor;
use App\Models\Product\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product\ProductColorImage;
use App\Models\Product\ProductTranslation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\hasManyThrough;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'slug',
        'sku',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'products_offers');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function colors(): HasMany
    {
        return $this->hasMany(ProductColor::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }

    /**
     * Get all images through colors relationship
     */
    public function images(): hasManyThrough
    {
        return $this->hasManyThrough(
            ProductColorImage::class,
            ProductColor::class,
            'product_id', // Foreign key on ProductColor table
            'product_color_id', // Foreign key on ProductColorImage table
            'id', // Local key on Product table
            'id' // Local key on ProductColor table
        );
    }

    /**
     * Scope to filter products with quantity strictly above a minimum
     */
    public function scopeAboveQuantity($query, int $minimum)
    {
        return $query->where('quantity', '>', $minimum);
    }

    /**
     * Scope to filter products within a low stock range (inclusive)
     */
    public function scopeLowStockRange($query, int $min = 1, int $max = 10)
    {
        return $query->whereBetween('quantity', [$min, $max]);
    }

    /**
     * Get the total stock including variants
     */
    public function getTotalStockAttribute(): int
    {
        return $this->quantity + $this->variants->sum('quantity');
    }

    /**
     * Get the minimum price from variants
     */
    public function getMinPriceAttribute(): float
    {
        return $this->variants->min('price') ?? 0.0;
    }

    /**
     * Get the maximum price from variants
     */
    public function getMaxPriceAttribute(): float
    {
        return $this->variants->max('price') ?? 0.0;
    }

    /**
     * Get the price range as a formatted string
     */
    public function getPriceRangeAttribute(): string
    {
        $min = $this->min_price;
        $max = $this->max_price;

        if ($min === $max) {
            return number_format($min, 2);
        }

        return number_format($min, 2) . ' - ' . number_format($max, 2);
    }

    /**
     * Get the total number of images across all colors
     */
    public function getTotalImagesCountAttribute(): int
    {
        return $this->colors->sum(fn($color) => $color->images->count());
    }

    /**
     * Check if product has variants
     */
    public function hasVariants(): bool
    {
        return $this->variants()->exists();
    }

    /**
     * Check if product has colors
     */
    public function hasColors(): bool
    {
        return $this->colors()->exists();
    }

    /**
     * Check if product has images
     */
    public function hasImages(): bool
    {
        return $this->images()->exists();
    }

    /**
     * Get available sizes from variants
     */
    public function getAvailableSizesAttribute(): array
    {
        return $this->variants->pluck('size')->unique()->values()->toArray();
    }

    /**
     * Get available colors
     */
    public function getAvailableColorsAttribute(): array
    {
        return $this->colors->pluck('color_code')->unique()->values()->toArray();
    }
}

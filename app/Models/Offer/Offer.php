<?php

namespace App\Models\Offer;

use Modules\Review\Entities\Review\Review;
use Modules\Order\Entities\OrderItem\OrderItem;
use Modules\Catalog\Entities\Product\Product;
use App\Models\Offer\OfferProduct;
use Modules\Catalog\Entities\Product\ProductColor;
use App\Models\Offer\OfferTranslation;
use Modules\Catalog\Entities\Product\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Offer extends Model
{
    use HasFactory;

    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    protected $fillable = [
        'image_path',
        'products_total_price',
        'offer_price',
        'start_date',
        'end_date',
        'status',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'products_offers', 'offer_id', 'product_id')
            ->withPivot(['product_variant_id', 'product_color_id', 'variant_price', 'quantity'])
            ->withTimestamps();
    }

    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'products_offers', 'offer_id', 'product_variant_id')
            ->withPivot(['product_id', 'product_color_id', 'variant_price', 'quantity'])
            ->withTimestamps();
    }

    public function productColors(): BelongsToMany
    {
        return $this->belongsToMany(ProductColor::class, 'products_offers', 'offer_id', 'product_color_id')
            ->withPivot(['product_id', 'product_variant_id', 'variant_price', 'quantity'])
            ->withTimestamps();
    }

    public function offerProducts()
    {
        return $this->hasMany(OfferProduct::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(OfferTranslation::class);
    }

    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(
            Review::class,
            OrderItem::class,
            'orderable_id', // Foreign key on OrderItem table
            'order_item_id', // Foreign key on Review table
            'id', // Local key on Product table
            'id' // Local key on OrderItem table
        )->where('orderable_type', static::class);
    }

    /**
     * Recalculate the total price based on selected products with variants and colors
     */
    public function recalculateTotalPrice(): void
    {
        $totalPrice = 0;

        // Get all related product variants with pivot data
        $variants = $this->productVariants()->withPivot(['quantity'])->get();

        foreach ($variants as $variant) {
            // Use amount_discount_price if available, otherwise price
            $price = $variant->amount_discount_price ?? $variant->price;
            $quantity = $variant->pivot->quantity ?? 1;
            $totalPrice += $price * $quantity;
        }

        $this->updateQuietly(['products_total_price' => $totalPrice]);
    }
}

<?php

namespace App\Models\Offer;

use App\Models\Product\Product;
use App\Models\Product\ProductColor;
use App\Models\Offer\OfferTranslation;
use App\Models\Offer\OfferProduct;
use App\Models\Product\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

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

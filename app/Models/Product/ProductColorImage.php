<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductColorImage extends Model
{
    protected $fillable = [
        'product_color_id',
        'image_url',
        'alt_text',
    ];

    protected $casts = [
        'product_color_id' => 'integer',
    ];

    public function productColor(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class);
    }

    /**
     * Get the product through the color relationship
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')
            ->through('productColor');
    }

    /**
     * Get the full URL for the image
     */
    public function getImageUrlAttribute($value): string
    {
        if (!$value) {
            return '';
        }

        // If it's already a full URL, return as is
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        // Otherwise, prepend the storage URL
        return asset('storage/' . $value);
    }

    /**
     * Get alt text or generate default
     */
    public function getAltTextAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        // Generate default alt text based on product and color
        $productColor = $this->productColor()->with('product.translations')->first();

        if ($productColor && $productColor->product) {
            $productName = $productColor->product->translations
                ->where('local', app()->getLocale())
                ->first()?->name ?? 'Product';

            return $productName . ' - ' . strtoupper($productColor->color_code);
        }

        return 'Product Image';
    }
}

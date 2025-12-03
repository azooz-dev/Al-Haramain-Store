<?php

namespace Modules\Catalog\Entities\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Catalog\Database\Factories\Product\ProductColorImageFactory;

class ProductColorImage extends Model
{
    use HasFactory;
    
    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ProductColorImageFactory::new();
    }
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
     * Get the full URL for the image (use this method when you need full URLs)
     */
    public function getFullImageUrlAttribute(): string
    {
        if (!$this->image_url) {
            return '';
        }

        // If it's already a full URL, return as is
        if (str_starts_with($this->image_url, 'http')) {
            return $this->image_url;
        }

        // Otherwise, prepend the storage URL
        return Storage::url($this->image_url);
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



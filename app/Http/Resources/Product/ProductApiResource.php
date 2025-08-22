<?php

namespace App\Http\Resources\Product;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $en = $this->translations->where('local', 'en')->first();
        $ar = $this->translations->where('local', 'ar')->first();
        return [
            'identifier' => $this->id,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'stock' => $this->quantity,
            'en' => [
                'title' => $en->name,
                'details' => $en->description
            ],
            'ar' => [
                'title' => $ar->name,
                'details' => $ar->description
            ],

            // Colors with images
            'colors' => $this->colors->map(function ($color) {
                return [
                    'id' => $color->id,
                    'color_code' => $color->color_code,
                    'images' => $color->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image_url' => $image->image_url,
                            'alt_text' => $image->alt_text,
                        ];
                    }),
                ];
            }),

            // Variants
            'variants' => $this->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'color_id' => $variant->color_id,
                    'size' => $variant->size,
                    'price' => $variant->price,
                    'amount_discount_price' => $variant->amount_discount_price,
                    'quantity' => $variant->quantity,
                ];
            }),

            'categories' => $this->categories->pluck('id')->toArray(),

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Computed attributes
            'total_stock' => $this->total_stock,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'price_range' => $this->price_range,
            'total_images_count' => $this->total_images_count,
            'available_sizes' => $this->available_sizes,
            'available_colors' => $this->available_colors,
        ];
    }
}

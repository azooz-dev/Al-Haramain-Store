<?php

namespace Modules\Catalog\Http\Resources\Product;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Review\ReviewApiResource;

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
                'title' => $en->name ?? "",
                'details' => $en->description ?? ""
            ],
            'ar' => [
                'title' => $ar->name ?? "",
                'details' => $ar->description ?? ""
            ],

            // Colors with images
            'colors' => $this->colors->map(function ($color) {
                return [
                    'id' => $color->id,
                    'color_code' => $color->color_code,
                    'images' => $color->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image_url' => $this->extractRelativeImagePath($image->getRawOriginal('image_url')),
                            'alt_text' => $image->alt_text ?? '',
                        ];
                    })->values()->toArray(),
                    // Variants
                    'variants' => $this->variants->where('color_id', $color->id)->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'color_id' => $variant->color_id,
                            'size' => $variant->size,
                            'price' => $variant->price,
                            'amount_discount_price' => $variant->amount_discount_price,
                            'quantity' => $variant->quantity,
                        ];
                    })->values()->toArray(),
                ];
            })->values()->toArray(),

            'reviews' => isset($this->reviews) ? ReviewApiResource::collection($this->reviews) : null,

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

    /**
     * Extract relative path from image URL for Filament FileUpload compatibility
     */
    private function extractRelativeImagePath(string $imageUrl): string
    {
        // If it's already a relative path, return as is
        if (!str_contains($imageUrl, 'http') && !str_contains($imageUrl, '/storage/')) {
            return $imageUrl;
        }

        // If it's a full URL with /storage/, extract the relative part
        if (str_contains($imageUrl, '/storage/')) {
            $parts = explode('/storage/', $imageUrl);
            return end($parts);
        }

        // If it starts with storage/, remove it
        if (str_starts_with($imageUrl, 'storage/')) {
            return substr($imageUrl, 8); // Remove 'storage/' prefix
        }

        // Return as is if we can't process it
        return $imageUrl;
    }

    /**
     * Get full image URL for API responses (when needed)
     */
    public function getFullImageUrl(string $relativePath): string
    {
        if (str_starts_with($relativePath, 'http')) {
            return $relativePath;
        }

        return asset('storage/' . $relativePath);
    }

    public static function transformAttribute($index)
    {
        $attributes = [
            'identifier' => 'id',
            'slug' => 'slug',
            'sku' => 'sku',
            'stock' => 'quantity',
            'en' => [
                'title' => 'name',
                'details' => 'description',
            ],
            'ar' => [
                'title' => 'name',
                'details' => 'description',
            ],
            'colors' => 'colors',
            'reviews' => 'reviews',
            'categories' => 'categories',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'total_stock' => 'total_stock',
            'min_price' => 'min_price',
            'max_price' => 'max_price',
            'price_range' => 'price_range',
            'total_images_count' => 'total_images_count',
            'available_sizes' => 'available_sizes',
            'available_colors' => 'available_colors',
        ];

        return $attributes[$index] ?? null;
    }
}

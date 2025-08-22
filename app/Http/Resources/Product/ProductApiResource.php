<?php

namespace App\Http\Resources\Product;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'images' => $this->images->map(function ($image) {
                return [
                    'image_url' => Storage::url($image->image_url),
                    'alt_text' => $image->alt_text,
                ];
            })->toArray(),
            'categories' => $this->categories->pluck('id')->toArray(),
            'variants' => $this->variants->map(function ($variant) {
                return [
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'quantity' => $variant->quantity,
                    'price' => $variant->price,
                    'amount_discount_price' => $variant->amount_discount_price,
                ];
            })->toArray(),

        ];
    }
}

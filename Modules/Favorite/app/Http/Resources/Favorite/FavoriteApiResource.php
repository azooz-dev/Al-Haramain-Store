<?php

namespace Modules\Favorite\Http\Resources\Favorite;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $en = $this->product->translations->where('local', 'en')->first();
        $ar = $this->product->translations->where('local', 'ar')->first();
        return [
            "identifier" => (int) $this->id,
            "product" => [
                "identifier" => $this->product->id,
                "slug" => $this->product->slug,
                "en" => [
                    "title" => $en->name ?? "",
                    "details" => $en->description ?? ""
                ],
                "ar" => [
                    "title" => $ar->name ?? "",
                    "details" => $ar->description ?? ""
                ],
                "color" => $this->product->colors->where('id', $this->color_id)->first(),
                "image" => $this->product->images->where('product_color_id', $this->color_id)->first(),
                "variant" => $this->product->variants->where("id", $this->variant_id)->first()
            ],
            'createdDate' => $this->created_at,
            "lastChange" => $this->updated_at
        ];
    }

    public static function transformAttribute($index)
    {
        $attributes = [
            'identifier' => 'id',
            'product' => 'product_id',
            'color' => 'color_id',
            'variant' => 'variant_id',
            'createdDate' => 'created_at',
            'lastChange' => 'updated_at'
        ];

        return $attributes[$index] ?? null;
    }
}

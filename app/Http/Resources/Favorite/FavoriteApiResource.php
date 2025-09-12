<?php

namespace App\Http\Resources\Favorite;

use App\Http\Resources\Product\ProductApiResource;
use App\Http\Resources\User\UserApiResource;
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
            "user" => new UserApiResource($this->user),
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
                "color" => $this->product->colors->where('id', $this->color_id),
                "image" => $this->product->images->where('color_id', $this->color_id)->first(),
                "variant" => $this->product->variants->where("id", $this->variant_id)
            ],
            'createdDate' => $this->created_at,
            "lastChange" => $this->updated_at
        ];
    }
}

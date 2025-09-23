<?php

namespace App\Http\Resources\Order\OrderItem;

use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Http\Resources\Offer\OfferApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "identifier" => (int) $this->id,
            "quantity" => (int) $this->quantity,
            "total_price" => (float) $this->total_price,
            "amount_discount_price" => (float) $this->amount_discount_price,
            "orderable" => $this->orderable_type === Product::class ? [
                'identifier' => (int) $this->orderable_id,
                'name' => $this->orderable->translations->where('local', app()->getLocale())->first()?->name ?? $this->orderable->translations->first()?->name ?? '',
                'sku' => $this->orderable->sku,
                'color' => $this->color->color_code,
                'images' => $this->color->images,
                'variant' => $this->variant->size,
                'price' => (float) $this->variant->price,
                'discount_price' => (float) $this->variant->amount_discount_price,
                "createdDate" => $this->created_at,
                "lastChange" => $this->updated_at
            ] : new OfferApiResource($this->orderable),
            'createdDate' => $this->created_at,
            'lastChange' => $this->updated_at
        ];
    }
}

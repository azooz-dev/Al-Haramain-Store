<?php

namespace App\Http\Resources\Order\OrderItem;

use App\Http\Resources\Product\ProductApiResource;
use Illuminate\Http\Request;
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
            "product" => new ProductApiResource($this->product),
            "createdDate" => $this->created_at,
            "lastChange" => $this->updated_at
        ];
    }
}

<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'identifier' => $this->id,
            'orderNumber' => $this->order_number,
            'total_amount' => $this->total_amount,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'user' => $this->user,
            'items' => $this->items,
            'address' => $this->address,
            'coupon' => $this->coupon,
            'reviews' => $this->reviews,
            'payments' => $this->payments,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

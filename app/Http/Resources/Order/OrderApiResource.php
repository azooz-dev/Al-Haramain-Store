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
            'totalAmount' => $this->total_amount,
            'paymentMethod' => $this->payment_method,
            'status' => $this->status,
            'customer' => $this->user,
            'items' => $this->items,
            'address' => $this->address,
            'coupon' => $this->coupon,
            'reviews' => $this->reviews,
            'payments' => $this->payments,
            'createdDate' => $this->created_at,
            'lastChange' => $this->updated_at,
        ];
    }


    public function transformAttributes($index)
    {
        $attributes = [
            'identifier' => "id",
            'orderNumber' => "order_number",
            'totalAmount' => "total_amount",
            'paymentMethod' => "payment_method",
            'status' => "status",
            'customer' => "user",
            'items' => "items",
            'address' => "address",
            'coupon' => "coupon",
            'reviews' => "reviews",
            'payments' => "payments",
            'createdDate' => "created_at",
            'lastChange' => "updated_at",
        ];

        return $attributes[$index] ?? null;
    }
}

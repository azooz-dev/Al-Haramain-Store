<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use App\Http\Resources\User\UserApiResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Order\OrderItem\OrderItemApiResource;
use App\Http\Resources\Review\ReviewApiResource;
use App\Http\Resources\User\UserAddresses\AddressApiResource;

class
OrderApiResource extends JsonResource
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
            'customer' => new UserApiResource($this->user),
            'items' => OrderItemApiResource::collection($this->items),
            'address' => new AddressApiResource($this->address),
            'coupon' => $this->coupon,
            'reviews' => $this->reviews->map(fn($review) => new ReviewApiResource($review)),
            'createdDate' => $this->created_at,
            'lastChange' => $this->updated_at,
        ];
    }


    public static function transformAttribute($index)
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

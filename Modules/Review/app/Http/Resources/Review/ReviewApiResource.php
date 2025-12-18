<?php

namespace Modules\Review\Http\Resources\Review;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\Http\Resources\Order\OrderItem\OrderItemApiResource;

class ReviewApiResource extends JsonResource
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
            'rating' => $this->rating,
            'locale' => $this->locale,
            'comment' => $this->comment,
            'status' => $this->status,
            'item' => new OrderItemApiResource($this->orderItem),
            'createdDate' => $this->created_at,
            'lastChange' => $this->updated_at,
        ];
    }

    public function transformAttribute($index)
    {
        $attribute = [
            'identifier' => 'id',
            'rating' => 'rating',
            'locale' => 'locale',
            'comment' => 'comment',
            'status' => 'status',
            'customer' => 'user',
            'item' => 'orderItem',
            'createdDate' => 'created_at',
            'lastChange' => 'updated_at',
        ];

        return $attribute[$index] ?? null;
    }
}

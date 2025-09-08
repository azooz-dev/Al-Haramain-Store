<?php

namespace App\Http\Resources\Review;

use App\Http\Resources\Product\ProductApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserApiResource;

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
            'status' => $this->status,1
            'customer' => new UserApiResource($this->user),
            'product' => new ProductApiResource($this->product),
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
            'product' => 'product',
            'createdDate' => 'created_at',
            'lastChange' => 'updated_at',
        ];

        return $attribute[$index] ?? null;
    }
}   

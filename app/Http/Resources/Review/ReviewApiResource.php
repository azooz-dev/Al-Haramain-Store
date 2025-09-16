<?php

namespace App\Http\Resources\Review;

use Illuminate\Http\Request;
use App\Http\Resources\User\UserApiResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Product\ProductApiResource;

class ReviewApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $en = $this->product->translations()->where('local', 'en')->first();
        $ar = $this->product->translations()->where('local', 'ar')->first();

        return [
            'identifier' => $this->id,
            'rating' => $this->rating,
            'locale' => $this->locale,
            'comment' => $this->comment,
            'status' => $this->status,
            'product' => [
                'identifier' => $this->product->id,
                'en' => [
                    'title' => $en->name,
                    'description' => $en->description,
                ],
                'ar' => [
                    'title' => $ar->name,
                    'description' => $ar->description,
                ],
            ],
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

<?php

namespace App\Http\Resources\Review;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'user' => $this->user,
            'createdDate' => $this->created_at,
            'lastChange' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $en = $this->translations->where('local', 'en')->first();
        $ar = $this->translations->where('local', 'ar')->first();

        return [
            'identifier' => $this->id,
            'slug' => $this->slug,
            'image' => $this->image,
            'en' => [
                'title' => $en?->name ?? '',
                'details' => $en?->description ?? '',
            ],
            'ar' => [
                'title' => $ar?->name ?? '',
                'details' => $ar?->description ?? '',
            ],
        ];
    }
}

<?php

namespace Modules\Catalog\Http\Resources\Category;

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
            'identifier' => (int) $this->id,
            'slug' => (string) $this->slug,
            'image' => (string) $this->image,
            'en' => [
                'title' => (string) $en?->name ?? '',
                'details' => (string) $en?->description ?? '',
            ],
            'ar' => [
                'title' => (string) $ar?->name ?? '',
                'details' => (string) $ar?->description ?? '',
            ],
        ];
    }


    public static function transformAttribute($index)
    {
        $attribute = [
            'identifier' => 'id',
            'slug' => 'slug',
            'image' => 'image',
            'en' => [
                'title' => 'name',
                'details' => 'description',
            ],
            'ar' => [
                'title' => 'name',
                'details' => 'description',
            ],
        ];

        return $attribute[$index] ?? null;
    }
}

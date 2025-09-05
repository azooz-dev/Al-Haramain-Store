<?php

namespace App\Http\Resources\Offer;

use App\Http\Resources\Product\ProductApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Product\ProductTranslationService;

class OfferApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $en = $this->translations->where('locale', 'en')->first();
        $ar = $this->translations->where('locale', 'ar')->first();

        return [
            'identifier' => $this->id,
            'picture' => $this->image_path,
            'discountType' => $this->discount_type,
            'discountAmount' => $this->discount_amount,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'status' => $this->status,
            'product' => new ProductApiResource($this->product),
            'en' => [
                'title' => $en->name,
                'details' => $en->description
            ],
            'ar' => [
                'title' => $ar->name,
                'details' => $ar->description
            ],
        ];
    }
}

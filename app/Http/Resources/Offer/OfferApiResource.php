<?php

namespace App\Http\Resources\Offer;

use App\Http\Resources\Product\ProductApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'productsTotalPrice' => $this->products_total_price,
            'offerPrice' => $this->offer_price,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'status' => $this->status,
            'products' => ProductApiResource::collection($this->products),
            'en' => [
                'title' => $en->name ?? '',
                'details' => $en->description ?? ''
            ],
            'ar' => [
                'title' => $ar->name ?? '',
                'details' => $ar->description ?? ''
            ],
        ];
    }

    public static function transformAttribute($index)
    {
        $attribute = [
            'identifier' => 'id',
            'picture' => 'image_path',
            'productsTotalPrice' => 'products_total_price',
            'offerPrice' => 'offer_price',
            'startDate' => "start_date",
            'endDate' => "end_date",
            'status' => "status",
            'products' => "products",
            'en' => [
                'title' => "name",
                'details' => "description",
            ],
            'ar' => [
                'title' => "name",
                'details' => "description",
            ],
        ];

        return $attribute[$index] ?? null;
    }
}

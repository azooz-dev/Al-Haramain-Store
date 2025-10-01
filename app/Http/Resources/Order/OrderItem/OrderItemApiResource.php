<?php

namespace App\Http\Resources\Order\OrderItem;

use Illuminate\Http\Request;
use App\Models\Product\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $en = $this->orderable->translations->where($this->orderable_type === Product::class ? 'local' : 'locale', 'en')->first();
        $ar = $this->orderable->translations->where($this->orderable_type === Product::class ? 'local' : 'locale', 'ar')->first();

        return [
            "identifier" => (int) $this->id,
            "quantity" => (int) $this->quantity,
            "total_price" => (float) $this->total_price,
            "amount_discount_price" => (float) $this->amount_discount_price,
            "orderable_type" => $this->orderable_type,
            "orderable" => $this->orderable_type === Product::class ? [
                'identifier' => (int) $this->orderable_id,
                'en' => [
                    'title' => $en->name ?? '',
                    'details' => $en->description ?? ''
                ],
                'ar' => [
                    'title' => $ar->name ?? '',
                    'details' => $ar->description ?? ''
                ],
                'sku' => $this->orderable->sku,
                'color' => $this->color->color_code,
                'images' => $this->color->images,
                'variant' => $this->variant->size,
                'price' => (float) $this->variant->price,
                'discount_price' => (float) $this->variant->amount_discount_price,
                "createdDate" => $this->created_at,
                "lastChange" => $this->updated_at
            ] : [
                'identifier' => (int) $this->orderable_id,
                'picture' => $this->orderable->image_path,
                'productsTotalPrice' => $this->orderable->products_total_price,
                'offerPrice' => $this->orderable->offer_price,
                'startDate' => $this->orderable->start_date,
                'endDate' => $this->orderable->end_date,
                'status' => $this->orderable->status,
                'en' => [
                    'title' => $en->name ?? '',
                    'details' => $en->description ?? ''
                ],
                'ar' => [
                    'title' => $ar->name ?? '',
                    'details' => $ar->description ?? ''
                ],
            ],
            'is_reviewed' => $this->is_reviewed,
            'createdDate' => $this->created_at,
            'lastChange' => $this->updated_at
        ];
    }
}

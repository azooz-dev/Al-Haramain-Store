<?php

namespace App\Http\Requests\Order;

use App\Models\Order\Order;
use App\Models\Product\Product;
use App\Models\Offer\Offer;
use App\Rules\ValidOrderItem;
use App\Services\Offer\OfferService;
use App\Services\Product\ProductService;
use App\Http\Requests\Order\BaseOrderRequest;
use App\Services\Product\Variant\ProductVariantService;

class OrderRequest extends BaseOrderRequest
{
    public function __construct(private ProductVariantService $variantService) {}
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'address_id' => 'required|exists:addresses,id',
            'coupon_id' => 'nullable|exists:coupons,id',
            'items' => 'required|array|min:1',
            'items.*' => new ValidOrderItem(app(ProductService::class), app(OfferService::class), $this->items),
            'items.*.orderable_type' => 'required|string|in:' . Product::class . ',' . Offer::class,
            'items.*.orderable_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.color_id' => 'nullable|integer',
            'items.*.variant_id' => 'nullable|integer',
            'payment_method' => 'required|in:' . Order::PAYMENT_METHOD_CASH_ON_DELIVERY . ',' . Order::PAYMENT_METHOD_CREDIT_CARD,
        ];
    }

    /**
     * Prepare the data for validation and transform orderable_type
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        // Transform orderable_type from string to full model class name
        if ($this->has('items')) {
            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                if (isset($item['orderable_type'])) {
                    $items[$index]['orderable_type'] = $this->transformOrderableType($item['orderable_type']);
                }
            }

            $this->merge(['items' => $items]);
        }
    }

    /**
     * Transform orderable_type string to full model class name
     *
     * @param string $type
     * @return string
     */
    private function transformOrderableType(string $type): string
    {
        return match ($type) {
            'product' => Product::class,
            'offer' => Offer::class,
            default => $type, // Return original if not recognized
        };
    }
}

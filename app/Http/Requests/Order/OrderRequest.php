<?php

namespace App\Http\Requests\Order;

use App\Models\Order\Order;
use App\Http\Requests\Order\BaseOrderRequest;
use App\Rules\ValidOrderItem;
use App\Services\Product\ProductService;
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
            'items.*' => new ValidOrderItem(app(ProductService::class), $this->items ?? []),
            'items.*.orderable_type' => 'required|string|in:product,offer',
            'items.*.orderable_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.color_id' => 'nullable|integer',
            'items.*.variant_id' => 'nullable|integer',
            'payment_method' => 'required|in:' . Order::PAYMENT_METHOD_CASH_ON_DELIVERY . ',' . Order::PAYMENT_METHOD_CREDIT_CARD,
        ];
    }
}

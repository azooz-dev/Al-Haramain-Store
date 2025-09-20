<?php

namespace App\Http\Requests\Order;

use App\Models\Order\Order;
use App\Http\Requests\Order\BaseOrderRequest;
use App\Rules\ValidOrderItem;
use App\Services\Offer\OfferService;
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
            'items.*' => [new ValidOrderItem(app(ProductService::class), app(OfferService::class), $this->items ?? [])],
            'items.*.orderable_type' => 'required|string|in:product,offer',
            'items.*.orderable_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.color_id' => 'required|exists:product_colors,id',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'payment_method' => 'required|in:' . Order::PAYMENT_METHOD_CASH_ON_DELIVERY . ',' . Order::PAYMENT_METHOD_CREDIT_CARD,
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('items')) {
                $items = $this->items;
                $productIds = collect($items)->pluck('product_id')->unique();
                $colorIds = collect($items)->pluck('color_id')->unique();
                $variantIds = collect($items)->pluck('variant_id')->unique();

                // Fetch all needed variants in one query
                $variants = $this->variantService->fetchAllVariants($productIds, $colorIds, $variantIds);

                // Build color map: [product_id-color_id] => true
                $colorMap = [];
                // Build variant map: [product_id-color_id-variant_id] => true
                $variantMap = [];
                foreach ($variants as $variant) {
                    $colorKey = $variant->product_id . '-' . $variant->color_id;
                    $variantKey = $colorKey . '-' . $variant->id;
                    $colorMap[$colorKey] = true;
                    $variantMap[$variantKey] = true;
                }

                foreach ($items as $index => $item) {
                    // Color check
                    if (isset($item['product_id'], $item['color_id'])) {
                        $colorKey = $item['product_id'] . '-' . $item['color_id'];
                        if (empty($colorMap[$colorKey])) {
                            $validator->errors()->add(
                                "items.{$index}.color_id",
                                __('app.messages.order.validation.color_not_found')
                            );
                        }
                    }
                    // Variant check
                    if (isset($item['product_id'], $item['color_id'], $item['variant_id'])) {
                        $variantKey = $item['product_id'] . '-' . $item['color_id'] . '-' . $item['variant_id'];
                        if (empty($variantMap[$variantKey])) {
                            $validator->errors()->add(
                                "items.{$index}.variant_id",
                                __('app.messages.order.validation.variant_not_found')
                            );
                        }
                    }
                }
            }
        });
    }
}

<?php

namespace App\Http\Requests\Order;

use App\Models\Order\Order;
use App\Http\Requests\Order\BaseOrderRequest;
use Modules\Catalog\Services\Product\Variant\ProductVariantService;

class OrderRequest extends BaseOrderRequest
{
    public function __construct(private ProductVariantService $variantService) {}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge($this->getCommonRules(), [
            'payment_intent_id' => 'required_if:payment_method,' . Order::PAYMENT_METHOD_CREDIT_CARD . '|nullable|string',
        ]);
    }
}

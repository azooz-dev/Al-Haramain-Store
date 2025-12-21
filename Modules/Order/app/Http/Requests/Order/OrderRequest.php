<?php

namespace Modules\Order\Http\Requests\Order;

use Modules\Payment\Enums\PaymentMethod;
use Modules\Order\Http\Requests\Order\BaseOrderRequest;
use Modules\Catalog\Contracts\ProductVariantServiceInterface;

class OrderRequest extends BaseOrderRequest
{
    public function __construct(private ProductVariantServiceInterface $variantService) {}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge($this->getCommonRules(), [
            'payment_intent_id' => 'required_if:payment_method,' . PaymentMethod::CREDIT_CARD->value . '|nullable|string',
        ]);
    }
}

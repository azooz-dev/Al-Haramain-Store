<?php

namespace App\Http\Requests\Payment;

use App\Models\Offer\Offer;
use App\Models\Order\Order;
use App\Rules\ValidOrderItem;
use App\Models\Product\Product;
use App\Services\Offer\OfferService;
use App\Services\Product\ProductService;
use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentIntentRequest extends FormRequest
{
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
            'coupon_code' => 'nullable|exists:coupons,code',
            'items' => 'required|array|min:1',
            'items.*' => new ValidOrderItem(app(ProductService::class), app(OfferService::class), $this->items),
            'items.*.orderable_type' => 'required|string|in:' . Product::class . ',' . Offer::class,
            'items.*.orderable_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.color_id' => 'nullable|integer',
            'items.*.variant_id' => 'nullable|integer',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:' . Order::PAYMENT_METHOD_CASH_ON_DELIVERY . ',' . Order::PAYMENT_METHOD_CREDIT_CARD,
        ];
    }
}

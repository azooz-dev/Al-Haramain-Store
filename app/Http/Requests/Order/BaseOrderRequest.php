<?php

namespace App\Http\Requests\Order;

use App\Models\Offer\Offer;
use App\Models\Order\Order;
use App\Rules\ValidOrderItem;
use App\Models\Product\Product;
use App\Http\Requests\BaseRequest;
use App\Services\Offer\OfferService;
use App\Services\Product\ProductService;
use App\Services\Product\Variant\ProductVariantService;

abstract class BaseOrderRequest extends BaseRequest
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
    abstract public function rules(): array;

    /**
     * Get common validation rules shared across order-related requests
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function getCommonRules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'address_id' => 'required|exists:addresses,id',
            'coupon_code' => 'nullable|exists:coupons,code',
            'items' => 'required|array|min:1',
            'items.*' => new ValidOrderItem(app(ProductService::class), app(OfferService::class), app(ProductVariantService::class), $this->items),
            'items.*.orderable_type' => 'required|string|in:' . Product::class . ',' . Offer::class,
            'items.*.orderable_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.color_id' => 'nullable|integer',
            'items.*.variant_id' => 'nullable|integer',
            'payment_method' => 'required|in:' . Order::PAYMENT_METHOD_CASH_ON_DELIVERY . ',' . Order::PAYMENT_METHOD_CREDIT_CARD,
        ];
    }

    /**
     * Prepare the data for validation and transform camelCase to snake_case
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $data = $this->all();
        $transformed = [];

        // Transform camelCase keys to snake_case
        foreach ($data as $key => $value) {
            $snakeKey = static::transformAttributes($key) ?? $this->camelToSnake($key);
            $transformed[$snakeKey] = $value;
        }

        // Transform orderable_type from string to full model class name
        if (isset($transformed['items']) && is_array($transformed['items'])) {
            foreach ($transformed['items'] as $index => $item) {
                if (isset($item['orderable_type'])) {
                    $transformed['items'][$index]['orderable_type'] = $this->transformOrderableType($item['orderable_type']);
                }
            }
        }

        $this->merge($transformed);
    }

    /**
     * Convert camelCase to snake_case as fallback
     *
     * @param string $key
     * @return string
     */
    private function camelToSnake(string $key): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
    }

    /**
     * Transform orderable_type string to full model class name
     *
     * @param string $type
     * @return string
     */
    protected function transformOrderableType(string $type): string
    {
        return match ($type) {
            'product' => Product::class,
            'offer' => Offer::class,
            default => $type, // Return original if not recognized
        };
    }


    public function attributes(): array
    {
        return [
            'id' => "identifier",
            "order_number" => __("validation.attributes.order_number"),
            "total_amount" => __("validation.attributes.total_amount"),
            "payment_method" => __("validation.attributes.payment_method"),
            "status" => __("validation.attributes.status"),
            "user_id" => __("validation.attributes.user"),
            "items" => __("validation.attributes.items"),
            "address_id" => __("validation.attributes.address"),
            "coupon_id" => __("validation.attributes.coupon"),
            "reviews" => __("validation.attributes.reviews"),
            "payments" => __("validation.attributes.payments"),
            "created_at" => __("validation.attributes.created_at"),
            "updated_at" => __("validation.attributes.updated_at"),
        ];
    }

    public static function transformAttributes($index)
    {
        $attribute = [
            "identifier" => "id",
            'orderNumber' => "order_number",
            'totalAmount' => "total_amount",
            'paymentMethod' => "payment_method",
            'status' => "status",
            'userId' => "user_id",
            'addressId' => "address_id",
            'couponCode' => "coupon_code",
            'items' => "items",
            'paymentIntentId' => "payment_intent_id",
            'createdAt' => "created_at",
            'updatedAt' => "updated_at",
        ];

        return $attribute[$index] ?? null;
    }
}

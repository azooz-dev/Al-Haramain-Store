<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;

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
            'customer' => "user",
            'items' => "items",
            'address' => "address",
            'coupon' => "coupon",
            'reviews' => "reviews",
            'payments' => "payments",
            'createdDate' => "created_at",
            'lastChange' => "updated_at",
        ];

        return $attribute[$index] ?? null;
    }
}

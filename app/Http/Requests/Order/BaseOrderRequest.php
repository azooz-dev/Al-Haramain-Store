<?php

namespace App\Http\Requests\Order;

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
            "order_number" => "orderNumber",
            "total_amount" => "totalAmount",
            "payment_method" => "paymentMethod",
            "status" => "status",
            "user_id" => "customer",
            "items" => "items",
            "address_id" => "address",
            "coupon_id" => "coupon",
            "reviews" => "reviews",
            "payments" => "payments",
            "created_at" => "createdDate",
            "updated_at" => "lastChange",
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

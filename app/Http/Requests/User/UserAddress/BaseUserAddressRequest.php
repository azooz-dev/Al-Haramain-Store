<?php

namespace App\Http\Requests\User\UserAddress;

use App\Http\Requests\BaseRequest;

abstract class BaseUserAddressRequest extends BaseRequest
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
            "id" => "identifier",
            "address_type" => __("validation.attributes.address_type"),
            "label" => __("validation.attributes.label"),
            "street" => __("validation.attributes.street"),
            "city" => __("validation.attributes.city"),
            "state" => __("validation.attributes.state"),
            "postal_code" => __("validation.attributes.postal_code"),
            "country" => __("validation.attributes.country"),
            "is_default" => __("validation.attributes.is_default"),
            "created_at" => __("validation.attributes.created_at"),
            "updated_at" => __("validation.attributes.updated_at"),
        ];
    }

    public static function transformAttributes($index)
    {
        $attribute = [
            "identifier" => "id",
            "addressType" => "address_type",
            "label" => "label",
            "street" => "street",
            "city" => "city",
            "state" => "state",
            "postalCode" => "postal_code",
            "country" => "country",
            "isDefault" => "is_default",
            "createdDate" => "created_at",
            "lastChange" => "updated_at"
        ];

        return $attribute[$index] ?? null;
    }
}

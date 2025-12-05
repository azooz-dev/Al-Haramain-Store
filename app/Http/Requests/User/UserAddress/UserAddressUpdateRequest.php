<?php

namespace App\Http\Requests\User\UserAddress;

use Modules\User\Entities\Address;
use App\Http\Requests\User\UserAddress\BaseUserAddressRequest;


class UserAddressUpdateRequest extends BaseUserAddressRequest
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
            "address_type" => "nullable|in:" . Address::ADDRESS_TYPE_HOME . "," . Address::ADDRESS_TYPE_WORK,
            "label" => "nullable|string|max:255",
            "street" => "nullable|string|max:255",
            "city" => "nullable|string|max:255",
            "state" => "nullable|string|max:255",
            "postal_code" => "nullable|numeric|digits:5",
            "country" => "nullable|string|max:255",
            "is_default" => "nullable|boolean"
        ];
    }
}

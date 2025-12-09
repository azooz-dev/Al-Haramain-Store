<?php

namespace Modules\User\app\Http\Requests\UserAddress;

use Modules\User\Entities\Address;

class UserAddressStoreRequest extends BaseUserAddressRequest
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
            "address_type" => "required|in:" . Address::ADDRESS_TYPE_HOME . "," . Address::ADDRESS_TYPE_WORK,
            "label" => "nullable|string|max:255",
            "street" => "required|string|max:255",
            "city" => "required|string|max:255",
            "state" => "required|string|max:255",
            "postal_code" => "required|numeric|digits:5",
            "country" => "required|string|max:255",
            "is_default" => "nullable|boolean"
        ];
    }
}

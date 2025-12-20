<?php

namespace Modules\User\app\Http\Requests\UserAddress;

use Modules\User\Enums\AddressType;

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
        $addressTypes = implode(',', array_column(AddressType::cases(), 'value'));
        
        return [
            "address_type" => "required|in:" . $addressTypes,
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

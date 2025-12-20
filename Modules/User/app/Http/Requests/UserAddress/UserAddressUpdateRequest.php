<?php

namespace Modules\User\app\Http\Requests\UserAddress;

use Modules\User\Enums\AddressType;

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
        $addressTypes = implode(',', array_column(AddressType::cases(), 'value'));
        
        return [
            "address_type" => "nullable|in:" . $addressTypes,
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

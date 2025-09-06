<?php

namespace App\Http\Requests\User;

use App\Http\Requests\User\BaseUserRequest;
use App\Models\User\User;

class UserStoreRequest extends BaseUserRequest
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
            'first_name'  => 'required|string',
            'last_name'   => 'required|string',
            'email'       => 'required|email|unique:users',
            'phone'       => 'required|numeric',
            'password'    => 'required|min:8|confirmed',
        ];
    }
}

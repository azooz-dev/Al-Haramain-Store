<?php

namespace App\Http\Requests\User;

use App\Http\Requests\User\BaseUserRequest;

class UserUpdateRequest extends BaseUserRequest
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
            'first_name' => "nullable|string",
            "last_name" => "nullable|string",
            "email" => "nullable|email|unique:users,email",
            "phone" => "nullable|numeric",
            "current_password" => "required_with:password|current_password",
            "password" => "nullable|min:8|confirmed",
        ];
    }
}

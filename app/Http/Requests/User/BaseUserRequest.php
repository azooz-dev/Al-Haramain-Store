<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

abstract class BaseUserRequest extends BaseRequest
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
            'id'          => 'identifier',
            'first_name'  => __("validation.attributes.first_name"),
            'last_name'   => __("validation.attributes.last_name"),
            'email'       => __('validation.attributes.email'),
            'phone'       => __('validation.attributes.phone'),
            'password'    => __('validation.attributes.password'),
            "current_password" => __('validation.attributes.current_password'),
            'created_at'  => __('validation.attributes.created_at'),
            'updated_at'  => __('validation.attributes.updated_at'),
        ];
    }


    public static function transformAttributes($index)
    {
        $attribute = [
            'identifier'  => 'id',
            'firstName'   => 'first_name',
            'lastName'    => 'last_name',
            'email'       => 'email',
            'phone'       => 'phone',
            'password'    => 'password',
            'currentPassword' => "current_password",
            'createdDate' => 'created_at',
            'lastChange'  => 'updated_at',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }
}

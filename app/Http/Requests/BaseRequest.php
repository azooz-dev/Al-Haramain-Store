<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use function App\Helpers\errorResponse;

abstract class BaseRequest extends FormRequest
{
    // Define the common attributes method to be implemented by subclasses
    public function attributes(): array
    {
        return [];
    }

    // Define the common transformAttributes method to be implemented by subclasses
    abstract public static function transformAttributes($index);

    // Common failed validation logic
    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->messages();

        // Don't transform attribute names for validation errors - keep original field names
        // Return validation errors in Laravel's standard format
        $response = response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $errors,
        ], 422);

        throw new HttpResponseException($response);
    }

    // Common preparation for validation logic
    protected function prepareForValidation()
    {
        foreach ($this->all() as $original => $value) {
            $originalName = $this->transformAttributes($original) ?? $original;
            $this->merge([$originalName => $value]);
        }
    }
}

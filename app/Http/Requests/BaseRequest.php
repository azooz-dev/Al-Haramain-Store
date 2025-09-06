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

        // Transform attribute names based on the mappings defined in attributes()
        $transformedErrors = [];
        foreach ($errors as $attribute => $errorMessages) {
            // Use the mapped name if it exists, otherwise keep the original attribute name
            $transformedAttribute = $this->attributes()[$attribute] ?? $attribute;
            $transformedErrors[$transformedAttribute] = $errorMessages;
        }

        $response = errorResponse($transformedErrors, 422);

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

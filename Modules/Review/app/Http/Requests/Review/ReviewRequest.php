<?php

namespace Modules\Review\Http\Requests\Review;

use Modules\Review\Entities\Review\Review;
use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
            'rating' => 'required|numeric|between:1,5',
            'comment' => 'required',
            'locale' => 'required|in:en,ar'
        ];
    }
}

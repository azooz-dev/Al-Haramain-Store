<?php

namespace Modules\Review\Http\Requests\Review;

use Modules\Review\Entities\Review\Review;
use App\Http\Requests\BaseRequest;

class ReviewRequest extends BaseRequest
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

    public function attributes(): array
    {
        return [
            'rating' => __('validation.attributes.rating'),
            'comment' => __('validation.attributes.comment'),
            'locale' => __('validation.attributes.locale'),
        ];
    }

    public static function transformAttributes($index)
    {
        $attribute = [
            'rating' => 'rating',
            'comment' => 'comment',
            'locale' => 'locale',
        ];

        return $attribute[$index] ?? null;
    }
}

<?php

namespace Modules\Catalog\Exceptions\Category;

use Exception;
use Illuminate\Http\JsonResponse;

use function App\Helpers\errorResponse;

class CategoryException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return errorResponse($this->getMessage() ?: __('app.messages.category.category_error'), $this->getCode() ?: 500);
    }
}

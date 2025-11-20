<?php

namespace App\Exceptions\Admin;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use function App\Helpers\errorResponse;

class AdminException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return errorResponse($this->getMessage() ?: __('app.messages.admin.error'), $this->getCode() ?: 500);
    }
}



<?php

namespace App\Exceptions\User\UserAddress;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use function App\Helpers\errorResponse;

class UserAddressException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return errorResponse($this->getMessage() ?: __("app.messages.user_address.user_address_error"), $this->getCode() ?: 500);
    }
}

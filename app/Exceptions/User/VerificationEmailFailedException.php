<?php

namespace App\Exceptions\User;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


use function App\Helpers\errorResponse;

class VerificationEmailFailedException extends UserException
{

  public function render(Request $request): JsonResponse
  {
    return errorResponse($this->getMessage() ?: __('app.messages.auth.verification_email_failed'), $this->getCode() ?: 500);
  }
}

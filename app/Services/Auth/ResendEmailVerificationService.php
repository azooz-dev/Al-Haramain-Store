<?php

namespace App\Services\Auth;

use App\Events\Auth\ResendVerificationEmail;
use function App\Helpers\errorResponse;
use App\Repositories\Interface\Auth\ResendEmailVerificationRepositoryInterface;

class ResendEmailVerificationService
{
  public function __construct(private ResendEmailVerificationRepositoryInterface $resendEmail) {}


  public function resend(int $userId)
  {
    $user = $this->resendEmail->findUserById($userId);

    if ($user->isVerified()) {
      return errorResponse(__("app.messages.auth.already_verified"), 400);
    }

    ResendVerificationEmail::dispatch($user);

    return $user;
  }
}

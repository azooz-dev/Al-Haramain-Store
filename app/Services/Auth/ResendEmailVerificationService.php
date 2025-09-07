<?php

namespace App\Services\Auth;

use App\Events\User\UserRegistered;
use App\Repositories\Interface\Auth\ResendEmailVerificationRepositoryInterface;

use function App\Helpers\errorResponse;

class ResendEmailVerificationService
{
  public function __construct(private ResendEmailVerificationRepositoryInterface $resendEmail) {}


  public function resend(int $userId)
  {
    $user = $this->resendEmail->findUserById($userId);

    if ($user->isVerified()) {
      return errorResponse(__("app.messages.auth.already_verified"), 400);
    }

    UserRegistered::dispatch($user);

    return $user;
  }
}

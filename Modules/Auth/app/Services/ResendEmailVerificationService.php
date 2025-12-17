<?php

namespace Modules\Auth\Services;

use Modules\Auth\Events\ResendVerificationEmail;
use function App\Helpers\errorResponse;
use Modules\Auth\Repositories\Interface\ResendEmailVerificationRepositoryInterface;

class ResendEmailVerificationService
{
  public function __construct(private ResendEmailVerificationRepositoryInterface $resendEmail) {}


  public function resend(string $userEmail)
  {
    $user = $this->resendEmail->findUserByEmail($userEmail);

    if ($user->isVerified()) {
      return errorResponse(__("app.messages.auth.already_verified"), 400);
    }

    ResendVerificationEmail::dispatch($user);

    return $user;
  }
}

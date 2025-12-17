<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\Password;
use App\Events\Auth\PasswordResetTokenCreated;
use Modules\Auth\Repositories\Interface\ForgetPasswordRepositoryInterface;

class ForgetPasswordService
{
  public function __construct(private ForgetPasswordRepositoryInterface $forgetPasswordRepository) {}


  public function forgetPassword(string $email)
  {
    $user = $this->forgetPasswordRepository->forget($email);

    if ($user) {
      $token = Password::broker()->createToken($user);

      event(new PasswordResetTokenCreated($user, $token));
    }

    return __("app.messages.auth.forgetPassword");
  }
}

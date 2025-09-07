<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Password;
use App\Events\PasswordResetTokenCreated;
use App\Repositories\Interface\Auth\ForgetPasswordRepositoryInterface;

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

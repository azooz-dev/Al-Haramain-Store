<?php

namespace App\Repositories\Eloquent\Auth;

use Modules\User\Entities\User;
use App\Repositories\Interface\Auth\EmailVerificationRepositoryInterface;

class EmailVerificationRepository implements EmailVerificationRepositoryInterface
{
  public function findUserByEmail(string $email)
  {
    return User::where('email', $email)->first();
  }
}

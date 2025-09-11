<?php

namespace App\Repositories\Eloquent\Auth;

use App\Models\User\User;
use App\Repositories\Interface\Auth\ResendEmailVerificationRepositoryInterface;

class ResendEmailVerificationRepository implements ResendEmailVerificationRepositoryInterface
{
  public function findUserByEmail(string $userEmail)
  {
    return User::where('email', $userEmail)->first();
  }
}

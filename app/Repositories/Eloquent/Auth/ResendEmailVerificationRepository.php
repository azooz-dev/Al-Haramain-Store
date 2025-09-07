<?php

namespace App\Repositories\Eloquent\Auth;

use App\Models\User\User;
use App\Repositories\Interface\Auth\ResendEmailVerificationRepositoryInterface;

class ResendEmailVerificationRepository implements ResendEmailVerificationRepositoryInterface
{
  public function findUserById(int $userId)
  {
    return User::findOrFail($userId);
  }
}

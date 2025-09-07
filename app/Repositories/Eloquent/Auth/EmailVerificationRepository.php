<?php

namespace App\Repositories\Eloquent\Auth;

use App\Models\User\User;
use App\Repositories\Interface\Auth\EmailVerificationRepositoryInterface;

class EmailVerificationRepository implements EmailVerificationRepositoryInterface
{
  public function findUserById(int $userId)
  {
    return User::findOrFail($userId);
  }
}

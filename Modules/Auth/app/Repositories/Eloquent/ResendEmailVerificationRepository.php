<?php

namespace Modules\Auth\Repositories\Eloquent;

use Modules\User\Entities\User;
use Modules\Auth\Repositories\Interface\ResendEmailVerificationRepositoryInterface;

class ResendEmailVerificationRepository implements ResendEmailVerificationRepositoryInterface
{
  public function findUserByEmail(string $userEmail)
  {
    return User::where('email', $userEmail)->first();
  }
}

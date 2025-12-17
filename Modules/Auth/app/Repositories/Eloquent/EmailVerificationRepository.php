<?php

namespace Modules\Auth\Repositories\Eloquent;

use Modules\User\Entities\User;
use Modules\Auth\Repositories\Interface\EmailVerificationRepositoryInterface;

class EmailVerificationRepository implements EmailVerificationRepositoryInterface
{
  public function findUserByEmail(string $email)
  {
    return User::where('email', $email)->first();
  }
}

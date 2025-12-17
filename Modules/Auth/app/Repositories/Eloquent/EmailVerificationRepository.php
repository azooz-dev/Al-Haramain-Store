<?php

namespace Modules\Auth\Repositories\Eloquent;

use Modules\User\Entities\User;
use App\Repositories\Interface\Auth\EmailVerificationRepositoryInterface;

class EmailVerificationRepository implements EmailVerificationRepositoryInterface
{
  public function findUserByEmail(string $email)
  {
    return User::where('email', $email)->first();
  }
}

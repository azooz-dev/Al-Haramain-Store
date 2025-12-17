<?php

namespace Modules\Auth\Repositories\Eloquent;

use Modules\User\Entities\User;
use Modules\Auth\Repositories\Interface\ForgetPasswordRepositoryInterface;

class ForgetPasswordRepository implements ForgetPasswordRepositoryInterface
{
  public function forget(string $email)
  {
    return User::where("email", $email)->first();
  }
}

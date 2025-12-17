<?php

namespace Modules\Auth\Repositories\Eloquent;

use Modules\User\Entities\User;
use App\Repositories\Interface\Auth\ForgetPasswordRepositoryInterface;

class ForgetPasswordRepository implements ForgetPasswordRepositoryInterface
{
  public function forget(string $email)
  {
    return User::where("email", $email)->first();
  }
}

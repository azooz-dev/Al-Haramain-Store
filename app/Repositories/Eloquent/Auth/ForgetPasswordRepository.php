<?php

namespace App\Repositories\Eloquent\Auth;

use App\Models\User\User;
use App\Repositories\Interface\Auth\ForgetPasswordRepositoryInterface;

class ForgetPasswordRepository implements ForgetPasswordRepositoryInterface
{
  public function forget(string $email)
  {
    return User::where("email", $email)->first();
  }
}

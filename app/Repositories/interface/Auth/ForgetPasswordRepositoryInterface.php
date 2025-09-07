<?php

namespace App\Repositories\Interface\Auth;

interface ForgetPasswordRepositoryInterface
{
  public function forget(string $email);
}

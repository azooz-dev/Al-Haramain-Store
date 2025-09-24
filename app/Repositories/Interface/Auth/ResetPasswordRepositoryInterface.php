<?php

namespace App\Repositories\Interface\Auth;

interface ResetPasswordRepositoryInterface
{
  public function reset(array $data);
}

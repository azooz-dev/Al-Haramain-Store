<?php

namespace App\Repositories\Interface\Auth;

interface EmailVerificationRepositoryInterface
{
  public function findUserByEmail(string $email);
}

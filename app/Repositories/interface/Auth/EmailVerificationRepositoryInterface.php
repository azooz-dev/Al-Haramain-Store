<?php

namespace App\Repositories\Interface\Auth;

interface EmailVerificationRepositoryInterface
{
  public function findUserById(int $userId);
}

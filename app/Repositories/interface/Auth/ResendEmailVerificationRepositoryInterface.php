<?php

namespace App\Repositories\Interface\Auth;


interface ResendEmailVerificationRepositoryInterface
{
  public function findUserById(int $userId);
}

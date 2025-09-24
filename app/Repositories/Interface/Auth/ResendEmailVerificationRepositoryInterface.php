<?php

namespace App\Repositories\Interface\Auth;


interface ResendEmailVerificationRepositoryInterface
{
  public function findUserByEmail(string $userEmail);
}

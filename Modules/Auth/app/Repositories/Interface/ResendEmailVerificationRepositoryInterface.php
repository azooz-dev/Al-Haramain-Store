<?php

namespace Modules\Auth\Repositories\Interface;


interface ResendEmailVerificationRepositoryInterface
{
  public function findUserByEmail(string $userEmail);
}
